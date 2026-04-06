<?php
/* =====================================================================
   api/user-dashboard/get-membership-plans.php
   Returns all active membership plans and the user's current plan ID.
   Used by the membership page and enroll-membership modal.

   Method: GET
   Auth:   Session required (user_id)

   Response data:
     plans[]           – all active membership plans
     current_plan_id   – plan_id of user's active membership (0 if none)
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

try {
    $db = Database::getInstance()->getConnection();

    // ── 1. All active membership plans ────────────────────────────────
    $stmt = $db->prepare(
        'SELECT id, name, description, price, duration_days,
                max_active_investments, withdrawal_speed_hours,
                referral_commission_pct, priority_support,
                has_analytics, has_strategy_reports,
                access_elite_plans, invitation_pools,
                color_accent, badge_icon, benefits,
                sort_order
         FROM   membership_plans
         WHERE  is_active = 1
         ORDER  BY sort_order ASC, id ASC'
    );
    $stmt->execute();
    $plans = $stmt->fetchAll();

    // Cast types and decode JSON benefits for clean response
    $plans = array_map(static function (array $p): array {
        $p['price']                   = (float)  $p['price'];
        $p['duration_days']           = (int)    $p['duration_days'];
        $p['max_active_investments']  = $p['max_active_investments'] !== null
                                            ? (int) $p['max_active_investments']
                                            : null;
        $p['withdrawal_speed_hours']  = (int)    $p['withdrawal_speed_hours'];
        $p['referral_commission_pct'] = (float)  $p['referral_commission_pct'];
        $p['has_analytics']           = (bool)   $p['has_analytics'];
        $p['has_strategy_reports']    = (bool)   $p['has_strategy_reports'];
        $p['access_elite_plans']      = (bool)   $p['access_elite_plans'];
        $p['invitation_pools']        = (bool)   $p['invitation_pools'];
        $p['benefits']                = $p['benefits']
                                            ? json_decode($p['benefits'], true)
                                            : [];
        return $p;
    }, $plans);

    // ── 2. User's current active membership plan ID ───────────────────
    $stmtCurrent = $db->prepare(
        'SELECT plan_id
         FROM   user_memberships
         WHERE  user_id  = :uid
           AND  status   = :status
           AND  end_date >= CURDATE()
         ORDER  BY end_date DESC
         LIMIT  1'
    );
    $stmtCurrent->execute([':uid' => $userId, ':status' => 'active']);
    $currentPlanId = (int) ($stmtCurrent->fetchColumn() ?: 0);

    echo json_encode([
        'success' => true,
        'data'    => [
            'plans'           => $plans,
            'current_plan_id' => $currentPlanId,
        ],
    ]);

} catch (Throwable $e) {
    error_log('[get-membership-plans] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load membership plans.']);
}
