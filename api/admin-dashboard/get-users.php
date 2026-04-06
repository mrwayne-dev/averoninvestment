<?php
/* =====================================================================
   api/admin-dashboard/get-users.php
   Paginated + filtered user list for admin panel.

   Method: GET
   Auth:   Admin session required
   Params: page, search, status, role
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

try {
    $db     = Database::getInstance()->getConnection();
    $page   = max(1, (int) ($_GET['page'] ?? 1));
    $limit  = 20;
    $offset = ($page - 1) * $limit;
    $search = trim($_GET['search'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $role   = trim($_GET['role']   ?? '');

    $where  = [];
    $params = [];

    if ($search) {
        $where[] = '(u.first_name LIKE :s1 OR u.last_name LIKE :s2 OR u.email LIKE :s3)';
        $params[':s1'] = '%' . $search . '%';
        $params[':s2'] = '%' . $search . '%';
        $params[':s3'] = '%' . $search . '%';
    }
    if ($status) { $where[] = 'u.status = :status'; $params[':status'] = $status; }
    if ($role)   { $where[] = 'u.role   = :role';   $params[':role']   = $role;   }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Count
    $stmtCount = $db->prepare('SELECT COUNT(*) FROM users u ' . $whereClause);
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    // Data
    $stmtData = $db->prepare(
        'SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.status, u.created_at,
                COALESCE(w.balance, 0)         AS balance,
                COALESCE(w.profit_balance, 0)  AS profit_balance,
                COALESCE(w.invested_amount, 0) AS invested_amount
         FROM users u
         LEFT JOIN wallets w ON w.user_id = u.id
         ' . $whereClause . '
         ORDER BY u.created_at DESC
         LIMIT ' . $limit . ' OFFSET ' . $offset
    );
    $stmtData->execute($params);
    $users = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as &$u) {
        $u['balance']         = (float) $u['balance'];
        $u['profit_balance']  = (float) $u['profit_balance'];
        $u['invested_amount'] = (float) $u['invested_amount'];
    }
    unset($u);

    echo json_encode([
        'success' => true,
        'data'    => [
            'users'  => $users,
            'total'  => $total,
            'page'   => $page,
            'pages'  => (int) ceil($total / $limit),
            'limit'  => $limit,
        ],
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
