<?php
/* =====================================================================
   api/user-dashboard/get-notifications.php
   Fetches notifications or marks them as read.

   GET  → Returns latest 50 notifications + unread_count
   POST → { id: int }   marks a single notification as read
          { all: true } marks ALL unread notifications as read

   Method: GET | POST
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

if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired']);
    exit;
}
$_SESSION['last_active'] = time();

// ── AJAX-only guard ───────────────────────────────────────────────────
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance()->getConnection();

    // ── GET — return notification list + unread count ─────────────────
    if ($method === 'GET') {
        $stmt = $db->prepare(
            'SELECT id, title, message, type, is_read, created_at
             FROM   notifications
             WHERE  user_id = :uid
             ORDER  BY created_at DESC
             LIMIT  50'
        );
        $stmt->execute([':uid' => $userId]);
        $notifications = $stmt->fetchAll();

        $countStmt = $db->prepare(
            'SELECT COUNT(*)
             FROM   notifications
             WHERE  user_id = :uid AND is_read = 0'
        );
        $countStmt->execute([':uid' => $userId]);
        $unreadCount = (int) $countStmt->fetchColumn();

        // Cast is_read to bool for clean JSON
        $notifications = array_map(static function (array $row): array {
            $row['is_read'] = (bool) $row['is_read'];
            return $row;
        }, $notifications);

        echo json_encode([
            'success' => true,
            'data'    => [
                'notifications' => $notifications,
                'unread_count'  => $unreadCount,
            ],
        ]);
        exit;
    }

    // ── POST — mark as read ───────────────────────────────────────────
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        // Mark ALL unread as read
        if (!empty($input['all'])) {
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
            exit;
        }

        // Mark single notification by ID
        $notifId = isset($input['id']) ? (int) $input['id'] : 0;

        if ($notifId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid notification ID.']);
            exit;
        }

        // Only mark if it belongs to this user (prevents IDOR)
        $stmt = $db->prepare(
            'UPDATE notifications
             SET    is_read = 1
             WHERE  id = :id AND user_id = :uid'
        );
        $stmt->execute([':id' => $notifId, ':uid' => $userId]);

        echo json_encode([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
        exit;
    }

    // ── Unsupported method ────────────────────────────────────────────
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);

} catch (Throwable $e) {
    error_log('[get-notifications] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to process notification request.']);
}
