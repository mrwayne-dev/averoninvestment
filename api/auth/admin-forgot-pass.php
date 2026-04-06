<?php
/* =====================================================================
   api/auth/admin-forgot-pass.php
   Sends a password-reset email to an admin account.

   Method: POST (JSON)
   Body:   { email }

   Security:
   - Only sends to users with role = 'admin'
   - Always returns success (never reveals if email exists)
   - Token expires after 30 minutes
   ===================================================================== */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed');
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($input['email'] ?? '');

if (empty($email) || !isValidEmail($email)) {
    sendJsonResponse(false, 'Please enter a valid email address');
}

// Generic success message — never reveal account existence
$genericOk = 'If that admin email is registered, a reset link has been sent';

try {
    $db   = Database::getInstance()->getConnection();
    $stmt = $db->prepare(
        "SELECT id, first_name, status, role
         FROM   users
         WHERE  email = :email
         LIMIT  1"
    );
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Must be an active admin
    if (!$user || $user['role'] !== 'admin' || $user['status'] !== 'active') {
        sendJsonResponse(true, $genericOk);
    }

    // Invalidate any existing unused tokens for this admin
    $db->prepare(
        "UPDATE password_resets
         SET    used_at = NOW()
         WHERE  user_id = :uid AND used_at IS NULL"
    )->execute(['uid' => $user['id']]);

    // Generate token — 30-minute expiry
    $token     = generateToken(64);
    $expiresAt = date('Y-m-d H:i:s', time() + 1800);

    $db->prepare(
        "INSERT INTO password_resets (user_id, token, expires_at)
         VALUES (:uid, :token, :expires_at)"
    )->execute([
        'uid'        => $user['id'],
        'token'      => $token,
        'expires_at' => $expiresAt,
    ]);

    // Admin-specific reset URL pointing to admin reset page
    $resetUrl = APP_URL . '/admin/reset-password?token=' . urlencode($token);

    // Reuse the email helper (same template, different URL)
    emailPasswordReset($email, $user['first_name'], $resetUrl);

    sendJsonResponse(true, $genericOk);

} catch (\Throwable $e) {
    error_log('[AdminForgotPass] ' . $e->getMessage());
    sendJsonResponse(false, 'Something went wrong. Please try again.');
}
