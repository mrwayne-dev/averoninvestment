<?php
/* =====================================================================
   api/user-dashboard/delete-account-request.php
   User requests account deletion — verifies password then emails admin.
   Does NOT delete the account automatically.

   Method: POST
   Auth:   requireAuth()
   Input:  { password: string }

   Validation:
     • password must match the user's current hash
     • simple self-service rate-limit: max 3 requests per 24 h

   Process:
     1. Verify password
     2. Insert a notification for the user (request received)
     3. Email admin with user details
     4. Email user with confirmation

   Returns: JSON { success, message }
   ===================================================================== */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed');
}

// ── CSRF validation ───────────────────────────────────────────
validateCsrfToken();

// ── Parse input ───────────────────────────────────────────────
$input    = json_decode(file_get_contents('php://input'), true) ?? [];
$password = $input['password'] ?? '';

if (empty($password)) {
    sendJsonResponse(false, 'Password is required.');
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // ── Fetch user ─────────────────────────────────────────────
    $stmt = $db->prepare("
        SELECT id, first_name, last_name, email, password, status
        FROM   users
        WHERE  id = :id
        LIMIT  1
    ");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!$user || $user['status'] === 'suspended') {
        sendJsonResponse(false, 'Account not found.');
    }

    // ── Verify password ────────────────────────────────────────
    if (!verifyPassword($password, $user['password'])) {
        sendJsonResponse(false, 'Incorrect password. Please try again.');
    }

    // ── Check for recent duplicate request (past 24 h) ────────
    $stmtCheck = $db->prepare("
        SELECT COUNT(*) AS cnt
        FROM   notifications
        WHERE  user_id = :uid
          AND  type    = 'delete_request'
          AND  created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmtCheck->execute([':uid' => $userId]);
    $row = $stmtCheck->fetch();

    if ((int) $row['cnt'] >= 3) {
        sendJsonResponse(false, 'You have already submitted a deletion request recently. Our team will process it within 72 hours.');
    }

    $now = date('Y-m-d H:i:s');

    // ── Insert notification ────────────────────────────────────
    $db->prepare("
        INSERT INTO notifications (user_id, title, message, type, created_at)
        VALUES (:uid, :title, :msg, 'delete_request', :now)
    ")->execute([
        ':uid'   => $userId,
        ':title' => 'Account Deletion Requested',
        ':msg'   => 'Your account deletion request has been received. Our team will review and process it within 72 hours. If this was a mistake, please contact support immediately.',
        ':now'   => $now,
    ]);

    // ── Email admin ────────────────────────────────────────────
    $adminEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
    if (!empty($adminEmail)) {
        emailAccountDeletionAdmin(
            $adminEmail,
            $user['first_name'] . ' ' . $user['last_name'],
            $user['email'],
            (string) $userId,
            $now
        );
    }

    // ── Email user (confirmation) ──────────────────────────────
    try {
        emailAccountDeletionUser(
            $user['email'],
            $user['first_name']
        );
    } catch (\Throwable $mailErr) {
        error_log('[delete-account-request] User email error: ' . $mailErr->getMessage());
    }

    sendJsonResponse(true, 'Your account deletion request has been submitted. Our team will process it within 72 hours.');

} catch (\Throwable $e) {
    error_log('[delete-account-request] ' . $e->getMessage());
    sendJsonResponse(false, 'Request failed. Please try again.');
}
