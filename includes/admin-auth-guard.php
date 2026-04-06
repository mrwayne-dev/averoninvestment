<?php
/**
 * Admin Auth Guard
 * ─────────────────────────────────────────────────────────────
 * Include at the TOP of every admin dashboard page.
 *
 * What it does:
 *  1. Starts session with secure cookie parameters
 *  2. Validates $_SESSION['user_id'] is present
 *  3. Checks role === 'admin'
 *  4. Enforces SESSION_TIMEOUT inactivity limit
 *  5. Rolls session ID every 5 minutes (anti-fixation)
 *  6. Generates per-session CSRF token
 *  7. Redirects non-admin requests to login
 *
 * After inclusion the following variables are available:
 *   $authUserId   — int    — authenticated admin's database ID
 *   $authUserName — string — first name (HTML-safe)
 *   $authRole     — string — always 'admin'
 *   $csrfToken    — string — for <meta name="csrf-token"> in <head>
 */

require_once dirname(__DIR__) . '/config/constants.php';

// ── Secure session cookie parameters ──────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,   // Switch to true in production (HTTPS)
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ── Step 1: Authentication check ──────────────────────────────
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// ── Step 2: Admin role check ───────────────────────────────────
if (($_SESSION['role'] ?? '') !== 'admin') {
    // Redirect non-admin users to user dashboard
    header('Location: /dashboard');
    exit;
}

// ── Step 3: Inactivity timeout ────────────────────────────────
if (
    isset($_SESSION['last_active']) &&
    (time() - (int) $_SESSION['last_active']) > SESSION_TIMEOUT
) {
    session_unset();
    session_destroy();
    header('Location: /login?reason=timeout');
    exit;
}

// ── Step 4: Session ID rotation (every 5 minutes) ─────────────
if (
    empty($_SESSION['_id_rolled']) ||
    (time() - (int) $_SESSION['_id_rolled']) > 300
) {
    session_regenerate_id(true);
    $_SESSION['_id_rolled'] = time();
}

// ── Step 5: Refresh inactivity clock ──────────────────────────
$_SESSION['last_active'] = time();

// ── Step 6: CSRF token ────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Convenience variables ──────────────────────────────────────
$authUserId   = (int) $_SESSION['user_id'];
$authUserName = htmlspecialchars($_SESSION['user_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
$authRole     = 'admin';
$csrfToken    = $_SESSION['csrf_token'];
