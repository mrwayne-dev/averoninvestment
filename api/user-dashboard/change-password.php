<?php
/* =====================================================================
   api/user-dashboard/change-password.php
   Verifies the current password, validates the new password strength,
   updates the hash, and sends a security notification email.

   Method: POST
   Auth:   Session required (user_id)
   Body:   { current_password, new_password, confirm_password }
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';

// ── Auth guard ────────────────────────────────────────────────────────
session_start();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired']);
    exit;
}
$_SESSION['last_active'] = time();

// ── Method + AJAX guards ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// ── Parse input ───────────────────────────────────────────────────────
$input           = json_decode(file_get_contents('php://input'), true) ?? [];
$currentPassword = $input['current_password'] ?? '';
$newPassword     = $input['new_password']     ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

// ── Validation ────────────────────────────────────────────────────────
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
    exit;
}

if ($currentPassword === $newPassword) {
    echo json_encode(['success' => false, 'message' => 'New password must be different from your current password.']);
    exit;
}

if (!isStrongPassword($newPassword)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters and include an uppercase letter, a number, and a special character.',
    ]);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // ── 1. Fetch current password hash ───────────────────────────────
    $stmt = $db->prepare(
        'SELECT first_name, email, password
         FROM   users
         WHERE  id = :uid
         LIMIT  1'
    );
    $stmt->execute([':uid' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    // ── 2. Verify current password ───────────────────────────────────
    if (!verifyPassword($currentPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit;
    }

    // ── 3. Hash and store new password ───────────────────────────────
    $newHash = hashPassword($newPassword);

    $db->prepare('UPDATE users SET password = :hash WHERE id = :uid')
       ->execute([':hash' => $newHash, ':uid' => $userId]);

    // ── 4. Insert security notification ─────────────────────────────
    $db->prepare(
        'INSERT INTO notifications (user_id, title, message, type)
         VALUES (:uid, :title, :message, :type)'
    )->execute([
        ':uid'     => $userId,
        ':title'   => 'Password Changed',
        ':message' => 'Your account password was successfully changed on '
                      . date('F j, Y \a\t H:i \U\T\C')
                      . '. If you did not make this change, contact support immediately.',
        ':type'    => 'security',
    ]);

    // ── 5. Send password-changed email (non-blocking) ────────────────
    $changeDate = date('F j, Y \a\t H:i \U\T\C');
    $ip         = getRealIP();
    emailPasswordChanged($user['email'], $user['first_name'], $changeDate, $ip);

    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully. Use your new password on your next login.',
    ]);

} catch (Throwable $e) {
    error_log('[change-password] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to change password. Please try again.']);
}
