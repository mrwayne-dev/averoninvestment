<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../utilities/helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$input    = json_decode(file_get_contents('php://input'), true) ?? [];
$email    = trim($input['email']    ?? '');
$password = $input['password']       ?? '';

if (empty($email) || empty($password)) {
    sendJsonResponse(false, 'Email and password are required');
}

try {
    $db   = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT id, first_name, email, password, role, status
        FROM users
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Constant-time check to prevent timing attacks
    $hash       = $user['password'] ?? '$2y$12$invalidhashtopreventtiming000000000000000000000000000';
    $passwordOk = verifyPassword($password, $hash);

    // Deny if: user not found, wrong password, not admin, or not active
    if (!$user || !$passwordOk || $user['role'] !== 'admin' || $user['status'] !== 'active') {
        sendJsonResponse(false, 'Invalid credentials');
    }

    // Update last_login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
    $stmt->execute(['id' => $user['id']]);

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    $_SESSION['user_id']     = (int) $user['id'];
    $_SESSION['admin_id']    = (int) $user['id'];   // Required by CLAUDE.md admin guard
    $_SESSION['user_email']  = $user['email'];
    $_SESSION['user_name']   = $user['first_name'];
    $_SESSION['role']        = 'admin';
    $_SESSION['last_active'] = time();

    sendJsonResponse(true, 'Login successful', [
        'redirect' => '/admin',
    ]);

} catch (\Throwable $e) {
    error_log('[AdminLogin] ' . $e->getMessage());
    sendJsonResponse(false, 'Login failed. Please try again.');
}
