<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$step  = (int) ($_GET['step'] ?? 0);
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($step) {
    case 1:  handleStep1($input); break;
    case 2:  handleStep2($input); break;
    case 3:  handleStep3($input); break;
    default: sendJsonResponse(false, 'Invalid registration step');
}

// ─────────────────────────────────────────────────────────
// STEP 1 — Personal details
// ─────────────────────────────────────────────────────────
function handleStep1(array $input): void {
    $firstName = trim($input['first_name'] ?? '');
    $lastName  = trim($input['last_name']  ?? '');
    $region    = trim($input['region']     ?? '');
    $language  = trim($input['language']   ?? '');

    if (empty($firstName))   sendJsonResponse(false, 'First name is required');
    if (empty($lastName))    sendJsonResponse(false, 'Last name is required');
    if (empty($region))      sendJsonResponse(false, 'Region is required');
    if (empty($language))    sendJsonResponse(false, 'Language is required');
    if (strlen($firstName) > 100) sendJsonResponse(false, 'First name is too long');
    if (strlen($lastName)  > 100) sendJsonResponse(false, 'Last name is too long');

    $_SESSION['reg_first_name'] = sanitize($firstName);
    $_SESSION['reg_last_name']  = sanitize($lastName);
    $_SESSION['reg_region']     = sanitize($region);
    $_SESSION['reg_language']   = sanitize($language);

    sendJsonResponse(true, 'Step 1 complete');
}

// ─────────────────────────────────────────────────────────
// STEP 2 — Email, password → create user + send code
// ─────────────────────────────────────────────────────────
function handleStep2(array $input): void {
    // Step 1 must be completed first
    if (empty($_SESSION['reg_first_name'])) {
        sendJsonResponse(false, 'Please complete step 1 first');
    }

    $email           = trim($input['email']            ?? '');
    $password        = $input['password']               ?? '';
    $confirmPassword = $input['confirm_password']       ?? '';

    // Validate email
    if (empty($email))          sendJsonResponse(false, 'Email is required');
    if (!isValidEmail($email))  sendJsonResponse(false, 'Invalid email address');

    // Validate password
    if (empty($password))                           sendJsonResponse(false, 'Password is required');
    if ($password !== $confirmPassword)             sendJsonResponse(false, 'Passwords do not match');
    if (!isStrongPassword($password)) {
        sendJsonResponse(false, 'Password must be at least 8 characters and include 1 uppercase letter, 1 number, and 1 special character');
    }

    try {
        $db = Database::getInstance()->getConnection();

        // ── Check if email is already in use ─────────────────────────
        $stmt = $db->prepare("SELECT id, status FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            if ($existingUser['status'] === 'active') {
                sendJsonResponse(false, 'An account with this email already exists. Please sign in.');
            }
            if ($existingUser['status'] === 'suspended') {
                sendJsonResponse(false, 'This account has been suspended. Please contact support.');
            }

            // status = 'pending' → account exists but was never verified.
            // Treat this as a resend: issue a fresh code without recreating the user.
            $pendingId = (int) $existingUser['id'];

            // Fetch the user's first name from DB in case session data is missing
            $nameStmt = $db->prepare("SELECT first_name FROM users WHERE id = :id LIMIT 1");
            $nameStmt->execute([':id' => $pendingId]);
            $firstName = $nameStmt->fetchColumn() ?: ($_SESSION['reg_first_name'] ?? '');

            $db->beginTransaction();

            // Expire all previous unused codes for this user
            $db->prepare(
                "UPDATE email_verifications SET used_at = NOW() WHERE user_id = :uid AND used_at IS NULL"
            )->execute([':uid' => $pendingId]);

            // Generate a fresh 6-digit code
            $code      = generateCode(6);
            $expiresAt = date('Y-m-d H:i:s', time() + CODE_EXPIRY);

            $db->prepare(
                "INSERT INTO email_verifications (user_id, code, expires_at) VALUES (:uid, :code, :exp)"
            )->execute([':uid' => $pendingId, ':code' => $code, ':exp' => $expiresAt]);

            $db->commit();

            // Prime session for step 3
            $_SESSION['reg_pending_user_id']    = $pendingId;
            $_SESSION['reg_pending_user_email'] = $email;
            if (empty($_SESSION['reg_first_name'])) {
                $_SESSION['reg_first_name'] = $firstName;
            }

            // Send verification email
            $emailSent = emailVerify($email, $firstName, $code);
            if (!$emailSent) {
                error_log('[Register Step2-Resend] Email delivery failed for ' . $email);
            }

            sendJsonResponse(true, 'Verification code sent to ' . $email);
        }

        // ── Brand-new registration ────────────────────────────────────
        $db->beginTransaction();

        $db->prepare("
            INSERT INTO users
                (first_name, last_name, email, password, role, status, region, language)
            VALUES
                (:first_name, :last_name, :email, :password, 'user', 'pending', :region, :language)
        ")->execute([
            ':first_name' => $_SESSION['reg_first_name'],
            ':last_name'  => $_SESSION['reg_last_name'],
            ':email'      => $email,
            ':password'   => hashPassword($password),
            ':region'     => $_SESSION['reg_region'],
            ':language'   => $_SESSION['reg_language'],
        ]);

        $userId = (int) $db->lastInsertId();

        // Create wallet
        $db->prepare("INSERT INTO wallets (user_id) VALUES (:user_id)")
           ->execute([':user_id' => $userId]);

        // Generate 6-digit verification code
        $code      = generateCode(6);
        $expiresAt = date('Y-m-d H:i:s', time() + CODE_EXPIRY);

        $db->prepare("
            INSERT INTO email_verifications (user_id, code, expires_at)
            VALUES (:user_id, :code, :expires_at)
        ")->execute([
            ':user_id'    => $userId,
            ':code'       => $code,
            ':expires_at' => $expiresAt,
        ]);

        $db->commit();

        // Store for step 3
        $_SESSION['reg_pending_user_id']    = $userId;
        $_SESSION['reg_pending_user_email'] = $email;

        // Send verification email
        $emailSent = emailVerify($email, $_SESSION['reg_first_name'], $code);
        if (!$emailSent) {
            error_log('[Register Step2] Email delivery failed for ' . $email);
        }

        sendJsonResponse(true, 'Verification code sent to ' . $email);

    } catch (\Throwable $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        error_log('[Register Step2] ' . $e->getMessage());
        // Expose error detail in dev mode only
        $msg = (defined('APP_ENV') && APP_ENV === 'development')
            ? 'Registration failed: ' . $e->getMessage()
            : 'Registration failed. Please try again.';
        sendJsonResponse(false, $msg);
    }
}

// ─────────────────────────────────────────────────────────
// STEP 3 — Verify code → activate account
// ─────────────────────────────────────────────────────────
function handleStep3(array $input): void {
    if (empty($_SESSION['reg_pending_user_id'])) {
        sendJsonResponse(false, 'Please complete steps 1 and 2 first');
    }

    $code   = trim($input['code'] ?? '');
    $userId = (int) $_SESSION['reg_pending_user_id'];

    if (empty($code) || !ctype_digit($code) || strlen($code) !== 6) {
        sendJsonResponse(false, 'Please enter the 6-digit code');
    }

    try {
        $db = Database::getInstance()->getConnection();

        // Find valid, unused code
        $stmt = $db->prepare("
            SELECT id FROM email_verifications
            WHERE user_id   = :user_id
              AND code       = :code
              AND expires_at > NOW()
              AND used_at   IS NULL
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId, 'code' => $code]);
        $verification = $stmt->fetch();

        if (!$verification) {
            sendJsonResponse(false, 'Invalid or expired code. Please request a new one.');
        }

        $db->beginTransaction();

        // Mark code as used
        $stmt = $db->prepare("UPDATE email_verifications SET used_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $verification['id']]);

        // Activate user
        $stmt = $db->prepare("UPDATE users SET status = 'active', last_login = NOW() WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        // Fetch user for session
        $stmt = $db->prepare("SELECT id, first_name, email, role FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        $db->commit();

        // Clear registration session data
        unset(
            $_SESSION['reg_first_name'],
            $_SESSION['reg_last_name'],
            $_SESSION['reg_region'],
            $_SESSION['reg_language'],
            $_SESSION['reg_referrer_id'],
            $_SESSION['reg_pending_user_id'],
            $_SESSION['reg_pending_user_email']
        );

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Set authenticated session
        $_SESSION['user_id']     = (int) $user['id'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_name']   = $user['first_name'];
        $_SESSION['role']        = $user['role'];
        $_SESSION['last_active'] = time();

        // Send welcome email (non-blocking)
        emailWelcome(
            $user['email'],
            $user['first_name'],
            $user['email'],
            APP_URL . '/dashboard'
        );

        sendJsonResponse(true, 'Account verified. Welcome!', [
            'redirect' => '/dashboard',
        ]);

    } catch (\Throwable $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        error_log('[Register Step3] ' . $e->getMessage());
        sendJsonResponse(false, 'Verification failed. Please try again.');
    }
}
