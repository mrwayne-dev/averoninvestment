<?php
/* =====================================================================
   api/auth/admin-register.php
   Invite-code-gated admin account creation.

   Method: POST (JSON)
   Body:   { invite_code, first_name, last_name, email, password, confirm_password }

   Security:
   - Requires a valid ADMIN_INVITE_CODE from constants/env
   - Email must not already exist
   - Password strength enforced
   - New account starts as role='admin', status='active'
   - Only callable when no admin account exists OR with valid invite code
   ===================================================================== */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../utilities/helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed');
}

$input          = json_decode(file_get_contents('php://input'), true) ?? [];
$inviteCode     = trim($input['invite_code']      ?? '');
$firstName      = trim($input['first_name']       ?? '');
$lastName       = trim($input['last_name']        ?? '');
$email          = trim($input['email']            ?? '');
$password       = $input['password']               ?? '';
$confirmPass    = $input['confirm_password']       ?? '';

// ── Validate invite code ───────────────────────────────────────
$validInviteCode = defined('ADMIN_INVITE_CODE') ? ADMIN_INVITE_CODE : ($_ENV['ADMIN_INVITE_CODE'] ?? '');

if (empty($validInviteCode)) {
    // Fallback: block registration entirely if no invite code configured
    sendJsonResponse(false, 'Admin registration is not configured on this server');
}

if (!hash_equals($validInviteCode, $inviteCode)) {
    sendJsonResponse(false, 'Invalid invite code');
}

// ── Validate fields ────────────────────────────────────────────
if (empty($firstName)) sendJsonResponse(false, 'First name is required');
if (empty($email) || !isValidEmail($email)) sendJsonResponse(false, 'Valid email address is required');
if (empty($password)) sendJsonResponse(false, 'Password is required');
if ($password !== $confirmPass) sendJsonResponse(false, 'Passwords do not match');

if (!isStrongPassword($password)) {
    sendJsonResponse(false, 'Password must be at least 8 characters and include 1 uppercase letter, 1 number, and 1 special character');
}

try {
    $db = Database::getInstance()->getConnection();

    // Check email uniqueness
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        sendJsonResponse(false, 'An account with this email already exists');
    }

    $db->beginTransaction();

    // Create admin user (no email_verified_at column in schema; invite code serves as verification)
    $stmt = $db->prepare(
        "INSERT INTO users
           (first_name, last_name, email, password, role, status, created_at)
         VALUES
           (:first_name, :last_name, :email, :password, 'admin', 'active', NOW())"
    );
    $stmt->execute([
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'email'      => $email,
        'password'   => hashPassword($password),
    ]);
    $newAdminId = (int) $db->lastInsertId();

    // Provision wallet row for admin (for platform accounting)
    $db->prepare(
        "INSERT INTO wallets (user_id, balance, profit_balance, invested_amount)
         VALUES (:uid, 0.00, 0.00, 0.00)
         ON DUPLICATE KEY UPDATE user_id = user_id"
    )->execute(['uid' => $newAdminId]);

    $db->commit();

    sendJsonResponse(true, 'Admin account created successfully. You can now log in.', [
        'redirect' => '/admin/login',
    ]);

} catch (\Throwable $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    error_log('[AdminRegister] ' . $e->getMessage());
    sendJsonResponse(false, 'Registration failed. Please try again.');
}
