<?php
/* =====================================================================
   api/user-dashboard/mark-notifications-read.php
   Marks all unread notifications as read for the authenticated user.
   Called by the "Mark all read" button in the notification dropdown.

   Method: POST
   Auth:   Session required (user_id)
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';

// ── Auth guard ────────────────────────────────────────────────────────
session_start();

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Session inactivity timeout
if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired']);
    exit;
}
$_SESSION['last_active'] = time();

// ── Method guard ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ── AJAX-only guard ───────────────────────────────────────────────────
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare(
        'UPDATE notifications
         SET    is_read = 1
         WHERE  user_id = :uid AND is_read = 0'
    );
    $stmt->execute([':uid' => $userId]);

    $affected = $stmt->rowCount();

    echo json_encode([
        'success' => true,
        'message' => 'All notifications marked as read.',
        'data'    => ['marked' => $affected],
    ]);

} catch (Throwable $e) {
    error_log('[mark-notifications-read] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update notifications.']);
}
