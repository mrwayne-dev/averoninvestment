<?php
/* =====================================================================
   api/user-dashboard/get-plans.php
   Returns all active investment plans for the start-investment modal.
   Also checks the user's membership plan limit vs current active count
   so the frontend can gate the form before submission.

   Method: GET
   Auth:   Session required (user_id)

   Response data:
     plans[]             – active investment plans
     active_count        – user's current active investment count
     max_investments     – membership limit (null = unlimited)
     limit_reached       – boolean convenience flag
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
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

    // ── 1. All active investment plans ────────────────────────────────
    $stmtPlans = $db->prepare(
        'SELECT id, name, description,
                min_amount, max_amount,
                duration_days,
                daily_yield_min, daily_yield_max,
                total_yield_min, total_yield_max,
                compounding_type, capital_locked,
                profit_withdrawal_after_days,
                dedicated_manager,
                color_accent, badge_label,
                sort_order
         FROM   investment_plans
         WHERE  is_active = 1
         ORDER  BY sort_order ASC, id ASC'
    );
    $stmtPlans->execute();
    $plans = $stmtPlans->fetchAll();

    // Cast numeric booleans for clean JSON
    $plans = array_map(static function (array $p): array {
        $p['capital_locked']      = (bool) $p['capital_locked'];
        $p['dedicated_manager']   = (bool) $p['dedicated_manager'];
        $p['min_amount']          = (float) $p['min_amount'];
        $p['max_amount']          = $p['max_amount'] !== null ? (float) $p['max_amount'] : null;
        $p['daily_yield_min']     = (float) $p['daily_yield_min'];
        $p['daily_yield_max']     = (float) $p['daily_yield_max'];
        $p['total_yield_min']     = (float) $p['total_yield_min'];
        $p['total_yield_max']     = (float) $p['total_yield_max'];
        $p['duration_days']       = (int)   $p['duration_days'];
        $p['profit_withdrawal_after_days'] = (int) $p['profit_withdrawal_after_days'];
        return $p;
    }, $plans);

    // ── 2. User's current active investment count ─────────────────────
    $stmtCount = $db->prepare(
        'SELECT COUNT(*)
         FROM   user_investments
         WHERE  user_id = :uid AND status = :status'
    );
    $stmtCount->execute([':uid' => $userId, ':status' => 'active']);
    $activeCount = (int) $stmtCount->fetchColumn();

    // ── 3. Membership investment limit ────────────────────────────────
    $stmtMembership = $db->prepare(
        'SELECT mp.max_active_investments
         FROM   user_memberships  um
         JOIN   membership_plans  mp ON mp.id = um.plan_id
         WHERE  um.user_id  = :uid
           AND  um.status   = :status
           AND  um.end_date >= CURDATE()
         ORDER  BY um.end_date DESC
         LIMIT  1'
    );
    $stmtMembership->execute([':uid' => $userId, ':status' => 'active']);
    $membershipRow = $stmtMembership->fetch();

    // Default to Basic Member limit (2) if the user has no active membership
    $maxInvestments = $membershipRow
        ? ($membershipRow['max_active_investments'] !== null
            ? (int) $membershipRow['max_active_investments']
            : null)
        : 2;

    $limitReached = $maxInvestments !== null && $activeCount >= $maxInvestments;

    // ── Response ──────────────────────────────────────────────────────
    echo json_encode([
        'success' => true,
        'data'    => [
            'plans'           => $plans,
            'active_count'    => $activeCount,
            'max_investments' => $maxInvestments,
            'limit_reached'   => $limitReached,
        ],
    ]);

} catch (Throwable $e) {
    error_log('[get-plans] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load investment plans.']);
}
