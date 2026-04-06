<?php
/* =====================================================================
   api/auth/admin-reset-pass.php
   Validates a password-reset token and updates the admin's password.

   Method: POST (JSON)
   Body:   { token, password, confirm_password }

   Security:
   - Token must exist, be unused, and not expired
   - Target user must have role = 'admin'
   - Invalidates token after first use
   - Sends password-changed confirmation email
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

$input           = json_decode(file_get_contents('php://input'), true) ?? [];
$token           = trim($input['token']            ?? '');
$password        = $input['password']               ?? '';
$confirmPassword = $input['confirm_password']       ?? '';

if (empty($token))    sendJsonResponse(false, 'Reset token is required');
if (empty($password)) sendJsonResponse(false, 'New password is required');

if ($password !== $confirmPassword) {
    sendJsonResponse(false, 'Passwords do not match');
}

if (!isStrongPassword($password)) {
    sendJsonResponse(false, 'Password must be at least 8 characters and include 1 uppercase letter, 1 number, and 1 special character');
}

try {
    $db = Database::getInstance()->getConnection();

    // Validate token — join users so we can verify role
    $stmt = $db->prepare(
        "SELECT pr.id, pr.user_id, u.first_name, u.email, u.role
         FROM   password_resets pr
         JOIN   users u ON u.id = pr.user_id
         WHERE  pr.token      = :token
           AND  pr.expires_at > NOW()
           AND  pr.used_at   IS NULL
         LIMIT  1"
    );
    $stmt->execute(['token' => $token]);
    $reset = $stmt->fetch();

    if (!$reset) {
        sendJsonResponse(false, 'This reset link is invalid or has expired. Please request a new one.');
    }

    // Verify the account is an admin
    if ($reset['role'] !== 'admin') {
        sendJsonResponse(false, 'This reset link is not valid for an admin account.');
    }

    $db->beginTransaction();

    // Update password
    $db->prepare("UPDATE users SET password = :password WHERE id = :id")
       ->execute([
           'password' => hashPassword($password),
           'id'       => $reset['user_id'],
       ]);

    // Invalidate token
    $db->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id")
       ->execute(['id' => $reset['id']]);

    $db->commit();

    // Confirmation email
    emailPasswordChanged(
        $reset['email'],
        $reset['first_name'],
        date('Y-m-d H:i:s') . ' UTC',
        getRealIP()
    );

    sendJsonResponse(true, 'Password updated successfully. You can now log in to the admin panel.');

} catch (\Throwable $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log('[AdminResetPass] ' . $e->getMessage());
    sendJsonResponse(false, 'Password reset failed. Please try again.');
}
