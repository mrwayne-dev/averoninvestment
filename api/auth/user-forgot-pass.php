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

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($input['email'] ?? '');

if (empty($email) || !isValidEmail($email)) {
    sendJsonResponse(false, 'Please enter a valid email address');
}

try {
    $db   = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT id, first_name, status
        FROM users
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Always return success — never reveal whether email exists
    if (!$user || $user['status'] !== 'active') {
        sendJsonResponse(true, 'If that email is registered, a reset link has been sent');
    }

    // Invalidate any existing unused tokens for this user
    $stmt = $db->prepare("
        UPDATE password_resets
        SET used_at = NOW()
        WHERE user_id = :user_id AND used_at IS NULL
    ");
    $stmt->execute(['user_id' => $user['id']]);

    // Generate token and expiry (30 minutes)
    $token     = generateToken(64);
    $expiresAt = date('Y-m-d H:i:s', time() + 1800);

    $stmt = $db->prepare("
        INSERT INTO password_resets (user_id, token, expires_at)
        VALUES (:user_id, :token, :expires_at)
    ");
    $stmt->execute([
        'user_id'    => $user['id'],
        'token'      => $token,
        'expires_at' => $expiresAt,
    ]);

    $resetUrl = APP_URL . '/forgot-password?token=' . urlencode($token);

    emailPasswordReset($email, $user['first_name'], $resetUrl);

    sendJsonResponse(true, 'If that email is registered, a reset link has been sent');

} catch (\Throwable $e) {
    error_log('[ForgotPassword] ' . $e->getMessage());
    sendJsonResponse(false, 'Something went wrong. Please try again.');
}
