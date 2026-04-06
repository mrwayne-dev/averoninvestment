<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../utilities/helper.php';
require_once '../utilities/rate-limiter.php';

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

if (!isValidEmail($email)) {
    sendJsonResponse(false, 'Invalid email address');
}

// ── Rate limit: 5 attempts per 15 minutes per IP ──────────────────────────────
$clientIp = getRealIP();
if (!RateLimiter::checkLimit('login', $clientIp, 5, 900)) {
    $wait = RateLimiter::retryAfter('login', $clientIp, 900);
    $mins = (int) ceil($wait / 60);
    sendJsonResponse(false, 'Too many login attempts. Please try again in ' . $mins . ' minute' . ($mins !== 1 ? 's' : '') . '.');
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

    // Always run verifyPassword to prevent timing attacks
    $hash       = $user['password'] ?? '$2y$12$invalidhashtopreventtiming000000000000000000000000000';
    $passwordOk = verifyPassword($password, $hash);

    if (!$user || !$passwordOk) {
        // Record failed attempt for rate limiting
        RateLimiter::recordAttempt('login', $clientIp);
        sendJsonResponse(false, 'Invalid email or password');
    }

    if ($user['status'] === 'pending') {
        sendJsonResponse(false, 'Please verify your email address before logging in');
    }

    if ($user['status'] === 'suspended') {
        sendJsonResponse(false, 'Your account has been suspended. Please contact support.');
    }

    // ── Successful login — clear failed attempts ──────────────────────────────
    RateLimiter::clearAttempts('login', $clientIp);

    // Update last_login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
    $stmt->execute(['id' => $user['id']]);

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    $_SESSION['user_id']     = (int) $user['id'];
    $_SESSION['user_email']  = $user['email'];
    $_SESSION['user_name']   = $user['first_name'];
    $_SESSION['role']        = $user['role'];
    $_SESSION['last_active'] = time();

    // Redirect admin to admin dashboard
    $redirect = ($user['role'] === 'admin')
        ? '/admin'
        : '/dashboard';

    sendJsonResponse(true, 'Login successful', ['redirect' => $redirect]);

} catch (\Throwable $e) {
    error_log('[UserLogin] ' . $e->getMessage());
    sendJsonResponse(false, 'Login failed. Please try again.');
}
