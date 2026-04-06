<?php
/* =====================================================================
   api/user-dashboard/get-transactions.php
   Paginated transaction history for the authenticated user.

   Query params:
     page      (int, default 1)
     limit     (int, default 20, max 100)
     type      (all|deposit|withdrawal|profit|membership_fee|referral_bonus|fee)
     status    (all|pending|confirmed|failed|cancelled)
     search    (string — matches reference or notes)
     date_from (YYYY-MM-DD)
     date_to   (YYYY-MM-DD)

   Response data:
     transactions[]  – current page rows
     total           – total matching rows
     pages           – total pages
     current_page    – page returned
     per_page        – limit used

   Method: GET
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

// ── Method + AJAX guards ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

// ── Parse + sanitise query params ────────────────────────────────────
$page     = max(1, (int) ($_GET['page']      ?? 1));
$limit    = min(100, max(1, (int) ($_GET['limit'] ?? 20)));
$type     = trim($_GET['type']      ?? 'all');
$status   = trim($_GET['status']    ?? 'all');
$search   = trim($_GET['search']    ?? '');
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo   = trim($_GET['date_to']   ?? '');

$allowedTypes    = ['all', 'deposit', 'withdrawal', 'profit', 'membership_fee', 'referral_bonus', 'fee'];
$allowedStatuses = ['all', 'pending', 'confirmed', 'failed', 'cancelled'];

if (!in_array($type, $allowedTypes, true))      $type   = 'all';
if (!in_array($status, $allowedStatuses, true)) $status = 'all';

try {
    $db = Database::getInstance()->getConnection();

    // ── Build dynamic WHERE clause ───────────────────────────────────
    $conditions = ['user_id = :uid'];
    $params     = [':uid' => $userId];

    if ($type !== 'all') {
        $conditions[] = 'type = :type';
        $params[':type'] = $type;
    }

    if ($status !== 'all') {
        $conditions[] = 'status = :status';
        $params[':status'] = $status;
    }

    if ($search !== '') {
        $conditions[] = '(reference LIKE :search OR notes LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    if ($dateFrom !== '' && strtotime($dateFrom) !== false) {
        $conditions[] = 'DATE(created_at) >= :date_from';
        $params[':date_from'] = $dateFrom;
    }

    if ($dateTo !== '' && strtotime($dateTo) !== false) {
        $conditions[] = 'DATE(created_at) <= :date_to';
        $params[':date_to'] = $dateTo;
    }

    $where = 'WHERE ' . implode(' AND ', $conditions);

    // ── Count total matching rows ────────────────────────────────────
    $countStmt = $db->prepare("SELECT COUNT(*) FROM transactions $where");
    $countStmt->execute($params);
    $total  = (int) $countStmt->fetchColumn();
    $pages  = $total > 0 ? (int) ceil($total / $limit) : 1;
    $offset = ($page - 1) * $limit;

    // ── Fetch current page (LIMIT/OFFSET are safe cast integers) ─────
    $dataStmt = $db->prepare(
        "SELECT id, type, amount, currency, status, reference, notes, processed_at, created_at
         FROM   transactions
         $where
         ORDER  BY created_at DESC
         LIMIT  $limit OFFSET $offset"
    );
    $dataStmt->execute($params);
    $transactions = $dataStmt->fetchAll();

    // Cast amount to float for consistent JSON
    $transactions = array_map(static function (array $tx): array {
        $tx['amount'] = (float) $tx['amount'];
        return $tx;
    }, $transactions);

    echo json_encode([
        'success' => true,
        'data'    => [
            'transactions' => $transactions,
            'total'        => $total,
            'pages'        => $pages,
            'current_page' => $page,
            'per_page'     => $limit,
        ],
    ]);

} catch (Throwable $e) {
    error_log('[get-transactions] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load transactions.']);
}
