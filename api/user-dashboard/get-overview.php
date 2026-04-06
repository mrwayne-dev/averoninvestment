<?php
/* =====================================================================
   api/user-dashboard/get-overview.php
   Dashboard polling endpoint — called every 30 s by dashboard.js.

   Returns:
     wallet            → balance, profit_balance, invested_amount,
                         active_plans, membership_tier, membership_expiry
     notifications_count → unread count (drives badge)
     notifications       → latest 5 rows (drives dropdown preview)
     recent_transactions → latest 8 rows (drives recent-activity table)

   Method: GET
   Auth:   Session required (user_id)
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';

// ── Auth: session guard ───────────────────────────────────────────────
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
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ── AJAX-only guard (basic CSRF mitigation for GET) ───────────────────
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // ── 1. Wallet balances ────────────────────────────────────────────
    $stmtWallet = $db->prepare(
        'SELECT balance, profit_balance, invested_amount
         FROM   wallets
         WHERE  user_id = :uid
         LIMIT  1'
    );
    $stmtWallet->execute([':uid' => $userId]);
    $wallet = $stmtWallet->fetch();

    // Lazily provision a wallet row if one was never created at registration
    if (!$wallet) {
        $db->prepare(
            'INSERT INTO wallets (user_id, balance, profit_balance, invested_amount)
             VALUES (:uid, 0.00, 0.00, 0.00)
             ON DUPLICATE KEY UPDATE user_id = user_id'
        )->execute([':uid' => $userId]);
        $wallet = ['balance' => '0.00', 'profit_balance' => '0.00', 'invested_amount' => '0.00'];
    }

    // ── 2. Active investment count ────────────────────────────────────
    $stmtPlans = $db->prepare(
        'SELECT COUNT(*)
         FROM   user_investments
         WHERE  user_id = :uid AND status = :status'
    );
    $stmtPlans->execute([':uid' => $userId, ':status' => 'active']);
    $activePlans = (int) $stmtPlans->fetchColumn();

    // ── 3. Active membership (latest non-expired row) ─────────────────
    $stmtMembership = $db->prepare(
        'SELECT mp.id      AS plan_id,
                mp.name    AS tier_name,
                um.end_date AS expiry
         FROM   user_memberships  um
         JOIN   membership_plans  mp ON mp.id = um.plan_id
         WHERE  um.user_id  = :uid
           AND  um.status   = :status
           AND  um.end_date >= CURDATE()
         ORDER  BY um.end_date DESC
         LIMIT  1'
    );
    $stmtMembership->execute([':uid' => $userId, ':status' => 'active']);
    $membership = $stmtMembership->fetch();

    // ── 4. Active investments (latest 5, for dashboard card list) ────
    $stmtActiveInv = $db->prepare(
        'SELECT ui.id,
                ui.amount,
                ui.profit_earned,
                ui.status,
                ui.start_date,
                ui.end_date,
                ui.daily_yield_rate,
                ip.name          AS plan_name,
                ip.duration_days,
                GREATEST(0, DATEDIFF(CURDATE(), ui.start_date)) AS days_elapsed
         FROM   user_investments ui
         JOIN   investment_plans ip ON ip.id = ui.plan_id
         WHERE  ui.user_id = :uid
           AND  ui.status  = :inv_status
         ORDER  BY ui.created_at DESC
         LIMIT  5'
    );
    $stmtActiveInv->execute([':uid' => $userId, ':inv_status' => 'active']);
    $activeInvestments = array_map(static function (array $r): array {
        $r['amount']           = (float) $r['amount'];
        $r['profit_earned']    = (float) $r['profit_earned'];
        $r['duration_days']    = (int)   $r['duration_days'];
        $r['days_elapsed']     = (int)   $r['days_elapsed'];
        $r['daily_yield_rate'] = (float) $r['daily_yield_rate'];
        return $r;
    }, $stmtActiveInv->fetchAll());

    // ── 5. Unread notification count ──────────────────────────────────
    $stmtNotifCount = $db->prepare(
        'SELECT COUNT(*)
         FROM   notifications
         WHERE  user_id = :uid AND is_read = 0'
    );
    $stmtNotifCount->execute([':uid' => $userId]);
    $notifCount = (int) $stmtNotifCount->fetchColumn();

    // ── 5. Notification preview (latest 5, read + unread) ────────────
    $stmtNotifs = $db->prepare(
        'SELECT id, title, message, type, is_read, created_at
         FROM   notifications
         WHERE  user_id = :uid
         ORDER  BY created_at DESC
         LIMIT  5'
    );
    $stmtNotifs->execute([':uid' => $userId]);
    $notifications = $stmtNotifs->fetchAll();

    // Cast is_read to bool for clean JSON
    $notifications = array_map(static function (array $row): array {
        $row['is_read'] = (bool) $row['is_read'];
        return $row;
    }, $notifications);

    // ── 6. Recent transactions (latest 8) ─────────────────────────────
    $stmtTx = $db->prepare(
        'SELECT id, type, amount, currency, status, reference, created_at
         FROM   transactions
         WHERE  user_id = :uid
         ORDER  BY created_at DESC
         LIMIT  8'
    );
    $stmtTx->execute([':uid' => $userId]);
    $recentTransactions = $stmtTx->fetchAll();

    // ── Response ──────────────────────────────────────────────────────
    echo json_encode([
        'success' => true,
        'data'    => [
            'wallet' => [
                'balance'           => $wallet['balance'],
                'profit_balance'    => $wallet['profit_balance'],
                'invested_amount'   => $wallet['invested_amount'],
                'active_plans'      => $activePlans,
                'membership_tier'    => $membership ? $membership['tier_name'] : null,
                'membership_expiry'  => $membership ? $membership['expiry']    : null,
                'membership_plan_id' => $membership ? (int) $membership['plan_id'] : null,
            ],
            'notifications_count' => $notifCount,
            'notifications'       => $notifications,
            'recent_transactions' => $recentTransactions,
            'active_investments'  => $activeInvestments,
        ],
    ]);

} catch (Throwable $e) {
    // Never expose internal errors to the client
    error_log('[get-overview] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load dashboard data.']);
}
