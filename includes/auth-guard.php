<?php
/**
 * Dashboard Auth Guard
 * ─────────────────────────────────────────────────────────────
 * Include at the TOP of every authenticated dashboard page.
 *
 * What it does:
 *  1. Starts the session with secure cookie parameters
 *  2. Validates $_SESSION['user_id'] is present
 *  3. Enforces SESSION_TIMEOUT inactivity limit
 *  4. Rolls the session ID every 5 minutes (anti-fixation)
 *  5. Generates a per-session CSRF token
 *  6. Redirects unauthenticated requests to login
 *
 * After inclusion the following variables are available:
 *   $authUserId   — int    — authenticated user's database ID
 *   $authUserName — string — first name (HTML-safe)
 *   $authRole     — string — 'user' | 'admin'
 *   $csrfToken    — string — for <meta name="csrf-token"> in <head>
 *
 * Usage example (dashboard page):
 *   <?php
 *   require_once '../../includes/auth-guard.php';
 *   $pageTitle = 'Dashboard';
 *   ?>
 *   <!DOCTYPE html>
 *   <html><?php include '../../includes/head.php'; ?> ...
 */

require_once dirname(__DIR__) . '/config/constants.php';

// ── Secure session cookie parameters ──────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,           // Expires when browser closes
        'path'     => '/',
        'secure'   => false,       // Switch to true behind HTTPS/production
        'httponly' => true,        // JavaScript cannot access session cookie
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ── Step 1: Authentication check ──────────────────────────────
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// ── Step 2: Inactivity timeout ────────────────────────────────
if (
    isset($_SESSION['last_active']) &&
    (time() - (int) $_SESSION['last_active']) > SESSION_TIMEOUT
) {
    session_unset();
    session_destroy();
    header('Location: /login?reason=timeout');
    exit;
}

// ── Step 3: Session ID rotation (every 5 minutes) ─────────────
// Prevents session fixation by issuing a fresh ID periodically.
if (
    empty($_SESSION['_id_rolled']) ||
    (time() - (int) $_SESSION['_id_rolled']) > 300
) {
    session_regenerate_id(true);
    $_SESSION['_id_rolled'] = time();
}

// ── Step 4: Refresh the inactivity clock ──────────────────────
$_SESSION['last_active'] = time();

// ── Step 5: CSRF token (generated once per session) ───────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Convenience variables available to the including page ─────
$authUserId   = (int) $_SESSION['user_id'];
$authUserName = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
$authRole     = $_SESSION['role'] ?? 'user';
$csrfToken    = $_SESSION['csrf_token'];
