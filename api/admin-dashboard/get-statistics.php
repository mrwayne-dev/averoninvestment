<?php
/* =====================================================================
   api/admin-dashboard/get-statistics.php
   Returns platform statistics for the admin dashboard.

   Query params:
     period  — 'today' | '7d' | '30d' | '90d' | 'custom' (default '30d')
     from    — YYYY-MM-DD (required when period=custom)
     to      — YYYY-MM-DD (required when period=custom)

   Returns:
     summary  → revenue, deposits, withdrawals, new_users, active_investments
     chart    → daily revenue data array for the selected period
     top_investors → top 10 users by invested amount
     recent_activity → latest 10 events across all users

   Method: GET
   Auth:   Admin session required
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';

// ── Auth guard ────────────────────────────────────────────────
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

// ── Resolve date range ─────────────────────────────────────────
$period = $_GET['period'] ?? '30d';
$today  = date('Y-m-d');

switch ($period) {
    case 'today':
        $dateFrom = $today;
        $dateTo   = $today;
        break;
    case '7d':
        $dateFrom = date('Y-m-d', strtotime('-6 days'));
        $dateTo   = $today;
        break;
    case '90d':
        $dateFrom = date('Y-m-d', strtotime('-89 days'));
        $dateTo   = $today;
        break;
    case 'custom':
        $dateFrom = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo   = $_GET['to']   ?? $today;
        // Basic validation
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            echo json_encode(['success' => false, 'message' => 'Invalid date format']);
            exit;
        }
        break;
    default: // '30d'
        $dateFrom = date('Y-m-d', strtotime('-29 days'));
        $dateTo   = $today;
}

try {
    $db = Database::getInstance()->getConnection();

    // ── 1. Summary stats ──────────────────────────────────────────
    // Total confirmed deposits in period
    $stmtDep = $db->prepare(
        'SELECT COALESCE(SUM(amount), 0) AS total
         FROM transactions
         WHERE type = :type AND status = :status
           AND DATE(created_at) BETWEEN :from AND :to'
    );
    $stmtDep->execute([':type' => 'deposit', ':status' => 'confirmed', ':from' => $dateFrom, ':to' => $dateTo]);
    $totalDeposits = (float) $stmtDep->fetchColumn();

    // Total confirmed withdrawals in period
    $stmtWith = $db->prepare(
        'SELECT COALESCE(SUM(amount), 0) AS total
         FROM transactions
         WHERE type = :type AND status IN (:s1, :s2)
           AND DATE(created_at) BETWEEN :from AND :to'
    );
    $stmtWith->execute([':type' => 'withdrawal', ':s1' => 'confirmed', ':s2' => 'completed', ':from' => $dateFrom, ':to' => $dateTo]);
    $totalWithdrawals = (float) $stmtWith->fetchColumn();

    // Platform revenue = deposits - withdrawals - profits
    $stmtProfit = $db->prepare(
        'SELECT COALESCE(SUM(amount), 0) AS total
         FROM transactions
         WHERE type = :type AND status = :status
           AND DATE(created_at) BETWEEN :from AND :to'
    );
    $stmtProfit->execute([':type' => 'profit', ':status' => 'confirmed', ':from' => $dateFrom, ':to' => $dateTo]);
    $totalProfits = (float) $stmtProfit->fetchColumn();

    $revenue = $totalDeposits - $totalWithdrawals - $totalProfits;

    // New users in period
    $stmtUsers = $db->prepare(
        'SELECT COUNT(*) FROM users
         WHERE DATE(created_at) BETWEEN :from AND :to'
    );
    $stmtUsers->execute([':from' => $dateFrom, ':to' => $dateTo]);
    $newUsers = (int) $stmtUsers->fetchColumn();

    // Active investments count
    $stmtInv = $db->prepare(
        'SELECT COUNT(*) FROM user_investments
         WHERE status = :status
           AND DATE(created_at) BETWEEN :from AND :to'
    );
    $stmtInv->execute([':status' => 'active', ':from' => $dateFrom, ':to' => $dateTo]);
    $activeInvestments = (int) $stmtInv->fetchColumn();

    // Total users count
    $totalUsers = (int) $db->query('SELECT COUNT(*) FROM users WHERE role = \'user\'')->fetchColumn();

    // Total wallet balance across all users
    $totalBalance = (float) $db->query('SELECT COALESCE(SUM(balance), 0) FROM wallets')->fetchColumn();

    // Total invested
    $totalInvested = (float) $db->query('SELECT COALESCE(SUM(invested_amount), 0) FROM wallets')->fetchColumn();

    // ── 2. Daily chart data ───────────────────────────────────────
    $stmtChart = $db->prepare(
        'SELECT
           DATE(created_at)              AS day,
           SUM(CASE WHEN type = \'deposit\'    AND status = \'confirmed\' THEN amount ELSE 0 END) AS deposits,
           SUM(CASE WHEN type = \'withdrawal\' AND status IN (\'confirmed\',\'completed\') THEN amount ELSE 0 END) AS withdrawals,
           SUM(CASE WHEN type = \'profit\'     AND status = \'confirmed\' THEN amount ELSE 0 END) AS profits
         FROM transactions
         WHERE DATE(created_at) BETWEEN :from AND :to
         GROUP BY DATE(created_at)
         ORDER BY day ASC'
    );
    $stmtChart->execute([':from' => $dateFrom, ':to' => $dateTo]);
    $chartRaw = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

    // Build a date-keyed map so we have every day in range (even if no transactions)
    $chartMap = [];
    foreach ($chartRaw as $row) {
        $chartMap[$row['day']] = $row;
    }

    $chartData = [];
    $cursor    = new DateTime($dateFrom);
    $end       = new DateTime($dateTo);
    while ($cursor <= $end) {
        $d = $cursor->format('Y-m-d');
        $chartData[] = [
            'date'        => $d,
            'deposits'    => isset($chartMap[$d]) ? (float) $chartMap[$d]['deposits']    : 0,
            'withdrawals' => isset($chartMap[$d]) ? (float) $chartMap[$d]['withdrawals'] : 0,
            'profits'     => isset($chartMap[$d]) ? (float) $chartMap[$d]['profits']     : 0,
        ];
        $cursor->modify('+1 day');
    }

    // ── 3. Top investors ─────────────────────────────────────────
    $stmtTop = $db->prepare(
        'SELECT u.id, u.first_name, u.last_name, u.email,
                w.balance, w.profit_balance, w.invested_amount,
                COUNT(ui.id) AS total_investments
         FROM users u
         LEFT JOIN wallets w ON w.user_id = u.id
         LEFT JOIN user_investments ui ON ui.user_id = u.id AND ui.status = \'active\'
         WHERE u.role = \'user\'
         GROUP BY u.id
         ORDER BY w.invested_amount DESC
         LIMIT 10'
    );
    $stmtTop->execute();
    $topInvestors = $stmtTop->fetchAll(PDO::FETCH_ASSOC);

    // Sanitize output
    foreach ($topInvestors as &$ti) {
        $ti['balance']          = (float) ($ti['balance'] ?? 0);
        $ti['profit_balance']   = (float) ($ti['profit_balance'] ?? 0);
        $ti['invested_amount']  = (float) ($ti['invested_amount'] ?? 0);
        $ti['total_investments'] = (int) $ti['total_investments'];
        unset($ti['email']); // don't expose email in top list for privacy
        $ti['name'] = trim($ti['first_name'] . ' ' . $ti['last_name']);
        unset($ti['first_name'], $ti['last_name']);
    }
    unset($ti);

    // ── 4. Recent activity ────────────────────────────────────────
    $stmtActivity = $db->prepare(
        'SELECT t.id, t.type, t.amount, t.status, t.created_at, t.reference,
                u.first_name, u.last_name, u.email
         FROM transactions t
         JOIN users u ON u.id = t.user_id
         ORDER BY t.created_at DESC
         LIMIT 10'
    );
    $stmtActivity->execute();
    $recentActivity = $stmtActivity->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recentActivity as &$ra) {
        $ra['amount'] = (float) $ra['amount'];
        $ra['name']   = trim($ra['first_name'] . ' ' . $ra['last_name']);
        unset($ra['first_name'], $ra['last_name']);
    }
    unset($ra);

    // ── 5. Transaction type breakdown ─────────────────────────────
    $stmtBreakdown = $db->prepare(
        'SELECT type, COUNT(*) AS count, COALESCE(SUM(amount), 0) AS total
         FROM transactions
         WHERE DATE(created_at) BETWEEN :from AND :to
         GROUP BY type'
    );
    $stmtBreakdown->execute([':from' => $dateFrom, ':to' => $dateTo]);
    $breakdown = $stmtBreakdown->fetchAll(PDO::FETCH_ASSOC);
    foreach ($breakdown as &$br) {
        $br['total'] = (float) $br['total'];
        $br['count'] = (int) $br['count'];
    }
    unset($br);

    echo json_encode([
        'success' => true,
        'data'    => [
            'period'     => ['from' => $dateFrom, 'to' => $dateTo, 'type' => $period],
            'summary'    => [
                'revenue'           => round($revenue, 2),
                'total_deposits'    => round($totalDeposits, 2),
                'total_withdrawals' => round($totalWithdrawals, 2),
                'total_profits'     => round($totalProfits, 2),
                'new_users'         => $newUsers,
                'total_users'       => $totalUsers,
                'active_investments'=> $activeInvestments,
                'total_balance'     => round($totalBalance, 2),
                'total_invested'    => round($totalInvested, 2),
            ],
            'chart'          => $chartData,
            'top_investors'  => $topInvestors,
            'recent_activity'=> $recentActivity,
            'breakdown'      => $breakdown,
        ],
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
