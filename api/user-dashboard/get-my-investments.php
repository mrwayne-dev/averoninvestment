<?php
/* =====================================================================
   api/user-dashboard/get-my-investments.php
   Returns all investments for the authenticated user, joined with
   plan details. Adds a computed progress_pct field.

   Method: GET
   Auth:   Session required (user_id)

   Response data:
     investments[]  – all user investment rows (active + completed)
       each row includes:
         id, amount, daily_yield_rate, profit_earned, status,
         start_date, end_date, profit_available_date, created_at,
         plan_name, duration_days, color_accent, badge_label,
         progress_pct  (0–100)
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

    $stmt = $db->prepare(
        'SELECT ui.id,
                ui.amount,
                ui.daily_yield_rate,
                ui.profit_earned,
                ui.status,
                ui.start_date,
                ui.end_date,
                ui.profit_available_date,
                ui.created_at,
                ip.name         AS plan_name,
                ip.duration_days,
                ip.color_accent,
                ip.badge_label
         FROM   user_investments ui
         JOIN   investment_plans ip ON ip.id = ui.plan_id
         WHERE  ui.user_id = :uid
         ORDER  BY ui.created_at DESC'
    );
    $stmt->execute([':uid' => $userId]);
    $investments = $stmt->fetchAll();

    // Compute progress_pct and cast types for clean JSON
    $today = new DateTimeImmutable('today', new DateTimeZone('UTC'));

    $investments = array_map(static function (array $inv) use ($today): array {
        $start   = new DateTimeImmutable($inv['start_date']);
        $end     = new DateTimeImmutable($inv['end_date']);
        $total   = (int) $start->diff($end)->days;
        $elapsed = (int) $start->diff($today)->days;

        if ($inv['status'] === 'completed') {
            $progress = 100;
        } elseif ($total > 0) {
            $progress = (int) min(100, round(($elapsed / $total) * 100));
        } else {
            $progress = 0;
        }

        $inv['progress_pct']    = $progress;
        $inv['amount']          = (float) $inv['amount'];
        $inv['profit_earned']   = (float) $inv['profit_earned'];
        $inv['daily_yield_rate'] = (float) $inv['daily_yield_rate'];
        $inv['duration_days']   = (int)   $inv['duration_days'];

        return $inv;
    }, $investments);

    echo json_encode([
        'success' => true,
        'data'    => ['investments' => $investments],
    ]);

} catch (Throwable $e) {
    error_log('[get-my-investments] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load investments.']);
}
