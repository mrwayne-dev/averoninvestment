<?php
/* =====================================================================
   api/admin-dashboard/manage-plans.php
   CRUD for investment_plans and membership_plans tables.

   Method: GET (list), POST (create/update/delete/toggle)
   Auth:   Admin session required

   Actions (POST body):
     create_investment   — Create a new investment plan
     update_investment   — Update existing investment plan
     delete_investment   — Delete investment plan (if no active investments)
     toggle_investment   — Toggle is_active flag
     create_membership   — Create a new membership plan
     update_membership   — Update existing membership plan
     delete_membership   — Delete membership plan (if no active memberships)
     toggle_membership   — Toggle is_active flag

   GET ?plan_type=investment|membership — List all plans
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once '../../config/database.php';
require_once '../../config/constants.php';

// ── Auth ──────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$adminId = (int) $_SESSION['user_id'];
$method  = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    try {
        $db        = Database::getInstance()->getConnection();
        $planType  = $_GET['plan_type'] ?? 'investment';

        if ($planType === 'membership') {
            $plans = $db->query(
                'SELECT * FROM membership_plans ORDER BY sort_order ASC, price ASC'
            )->fetchAll(PDO::FETCH_ASSOC);
            foreach ($plans as &$p) {
                $p['price']                   = (float) $p['price'];
                $p['referral_commission_pct'] = (float) $p['referral_commission_pct'];
                $p['is_active']               = (bool)  $p['is_active'];
                $p['has_analytics']           = (bool)  $p['has_analytics'];
                $p['has_strategy_reports']    = (bool)  $p['has_strategy_reports'];
                $p['access_elite_plans']      = (bool)  $p['access_elite_plans'];
                $p['invitation_pools']        = (bool)  $p['invitation_pools'];
            }
            unset($p);
        } else {
            $plans = $db->query(
                'SELECT * FROM investment_plans ORDER BY sort_order ASC, min_amount ASC'
            )->fetchAll(PDO::FETCH_ASSOC);
            foreach ($plans as &$p) {
                $p['min_amount']               = (float) $p['min_amount'];
                $p['max_amount']               = $p['max_amount'] !== null ? (float) $p['max_amount'] : null;
                $p['daily_yield_min']          = (float) $p['daily_yield_min'];
                $p['daily_yield_max']          = (float) $p['daily_yield_max'];
                $p['total_yield_min']          = (float) $p['total_yield_min'];
                $p['total_yield_max']          = (float) $p['total_yield_max'];
                $p['is_active']                = (bool)  $p['is_active'];
                $p['capital_locked']           = (bool)  $p['capital_locked'];
                $p['dedicated_manager']        = (bool)  $p['dedicated_manager'];
            }
            unset($p);
        }

        echo json_encode(['success' => true, 'data' => ['plans' => $plans]]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// ── Helper: sanitize investment plan fields ────────────────────
function sanitizeInvestmentPlan(array $d): array {
    return [
        'name'                        => trim($d['name'] ?? ''),
        'description'                 => trim($d['description'] ?? ''),
        'min_amount'                  => max(0, (float) ($d['min_amount'] ?? 0)),
        'max_amount'                  => isset($d['max_amount']) && $d['max_amount'] !== '' && $d['max_amount'] !== null
                                           ? max(0, (float) $d['max_amount']) : null,
        'duration_days'               => max(1, (int)   ($d['duration_days'] ?? 30)),
        'daily_yield_min'             => max(0, (float) ($d['daily_yield_min'] ?? 0)),
        'daily_yield_max'             => max(0, (float) ($d['daily_yield_max'] ?? 0)),
        'total_yield_min'             => max(0, (float) ($d['total_yield_min'] ?? 0)),
        'total_yield_max'             => max(0, (float) ($d['total_yield_max'] ?? 0)),
        'compounding_type'            => in_array($d['compounding_type'] ?? '', ['simple','compound']) ? $d['compounding_type'] : 'simple',
        'capital_locked'              => !empty($d['capital_locked']) ? 1 : 0,
        'profit_withdrawal_after_days'=> max(0, (int)   ($d['profit_withdrawal_after_days'] ?? 0)),
        'dedicated_manager'           => !empty($d['dedicated_manager']) ? 1 : 0,
        'color_accent'                => preg_match('/^#[0-9A-Fa-f]{6}$/', $d['color_accent'] ?? '') ? $d['color_accent'] : '#2196F3',
        'badge_label'                 => trim($d['badge_label'] ?? ''),
        'sort_order'                  => max(0, (int) ($d['sort_order'] ?? 0)),
    ];
}

// ── Helper: sanitize membership plan fields ────────────────────
function sanitizeMembershipPlan(array $d): array {
    $supportOptions = ['standard','priority','dedicated','manager'];
    return [
        'name'                     => trim($d['name'] ?? ''),
        'description'              => trim($d['description'] ?? ''),
        'price'                    => max(0, (float) ($d['price'] ?? 0)),
        'duration_days'            => max(1, (int)   ($d['duration_days'] ?? 30)),
        'max_active_investments'   => isset($d['max_active_investments']) && $d['max_active_investments'] !== ''
                                        ? max(1, (int) $d['max_active_investments']) : null,
        'withdrawal_speed_hours'   => max(1, (int)   ($d['withdrawal_speed_hours'] ?? 72)),
        'referral_commission_pct'  => max(0, (float) ($d['referral_commission_pct'] ?? 0)),
        'priority_support'         => in_array($d['priority_support'] ?? '', $supportOptions) ? $d['priority_support'] : 'standard',
        'has_analytics'            => !empty($d['has_analytics'])         ? 1 : 0,
        'has_strategy_reports'     => !empty($d['has_strategy_reports'])  ? 1 : 0,
        'access_elite_plans'       => !empty($d['access_elite_plans'])    ? 1 : 0,
        'invitation_pools'         => !empty($d['invitation_pools'])      ? 1 : 0,
        'color_accent'             => preg_match('/^#[0-9A-Fa-f]{6}$/', $d['color_accent'] ?? '') ? $d['color_accent'] : '#A0A0A0',
        'badge_icon'               => trim($d['badge_icon'] ?? ''),
        'sort_order'               => max(0, (int) ($d['sort_order'] ?? 0)),
    ];
}

try {
    $db = Database::getInstance()->getConnection();

    switch ($action) {

        // ── Investment plan CRUD ──────────────────────────────────
        case 'create_investment':
            $p = sanitizeInvestmentPlan($input);
            if (!$p['name']) {
                echo json_encode(['success' => false, 'message' => 'Plan name is required']);
                exit;
            }
            $stmt = $db->prepare(
                'INSERT INTO investment_plans
                   (name, description, min_amount, max_amount, duration_days,
                    daily_yield_min, daily_yield_max, total_yield_min, total_yield_max,
                    compounding_type, capital_locked, profit_withdrawal_after_days,
                    dedicated_manager, color_accent, badge_label, sort_order,
                    is_active, created_by)
                 VALUES
                   (:name, :desc, :min, :max, :dur,
                    :dymin, :dymax, :tymin, :tymax,
                    :comp, :locked, :pwa,
                    :mgr, :color, :badge, :sort,
                    1, :admin)'
            );
            $stmt->execute([
                ':name'  => $p['name'],    ':desc'  => $p['description'],
                ':min'   => $p['min_amount'], ':max' => $p['max_amount'],
                ':dur'   => $p['duration_days'],
                ':dymin' => $p['daily_yield_min'], ':dymax' => $p['daily_yield_max'],
                ':tymin' => $p['total_yield_min'], ':tymax' => $p['total_yield_max'],
                ':comp'  => $p['compounding_type'], ':locked' => $p['capital_locked'],
                ':pwa'   => $p['profit_withdrawal_after_days'],
                ':mgr'   => $p['dedicated_manager'],
                ':color' => $p['color_accent'], ':badge' => $p['badge_label'],
                ':sort'  => $p['sort_order'], ':admin' => $adminId,
            ]);
            echo json_encode(['success' => true, 'message' => 'Investment plan created', 'data' => ['id' => (int)$db->lastInsertId()]]);
            break;

        case 'update_investment':
            $planId = (int) ($input['plan_id'] ?? 0);
            if (!$planId) { echo json_encode(['success' => false, 'message' => 'plan_id required']); exit; }
            $p = sanitizeInvestmentPlan($input);
            $db->prepare(
                'UPDATE investment_plans SET
                   name = :name, description = :desc,
                   min_amount = :min, max_amount = :max, duration_days = :dur,
                   daily_yield_min = :dymin, daily_yield_max = :dymax,
                   total_yield_min = :tymin, total_yield_max = :tymax,
                   compounding_type = :comp, capital_locked = :locked,
                   profit_withdrawal_after_days = :pwa, dedicated_manager = :mgr,
                   color_accent = :color, badge_label = :badge, sort_order = :sort,
                   updated_at = NOW()
                 WHERE id = :id'
            )->execute([
                ':name'  => $p['name'],    ':desc'  => $p['description'],
                ':min'   => $p['min_amount'], ':max' => $p['max_amount'],
                ':dur'   => $p['duration_days'],
                ':dymin' => $p['daily_yield_min'], ':dymax' => $p['daily_yield_max'],
                ':tymin' => $p['total_yield_min'], ':tymax' => $p['total_yield_max'],
                ':comp'  => $p['compounding_type'], ':locked' => $p['capital_locked'],
                ':pwa'   => $p['profit_withdrawal_after_days'],
                ':mgr'   => $p['dedicated_manager'],
                ':color' => $p['color_accent'], ':badge' => $p['badge_label'],
                ':sort'  => $p['sort_order'], ':id' => $planId,
            ]);
            echo json_encode(['success' => true, 'message' => 'Investment plan updated']);
            break;

        case 'delete_investment':
            $planId = (int) ($input['plan_id'] ?? 0);
            if (!$planId) { echo json_encode(['success' => false, 'message' => 'plan_id required']); exit; }
            // Safety check — no active investments on this plan
            $stmtCheck = $db->prepare('SELECT COUNT(*) FROM user_investments WHERE plan_id = :id AND status = \'active\'');
            $stmtCheck->execute([':id' => $planId]);
            if ((int) $stmtCheck->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete plan with active investments. Deactivate it instead.']);
                exit;
            }
            $db->prepare('DELETE FROM investment_plans WHERE id = :id')->execute([':id' => $planId]);
            echo json_encode(['success' => true, 'message' => 'Investment plan deleted']);
            break;

        case 'toggle_investment':
            $planId = (int) ($input['plan_id'] ?? 0);
            if (!$planId) { echo json_encode(['success' => false, 'message' => 'plan_id required']); exit; }
            $db->prepare('UPDATE investment_plans SET is_active = NOT is_active, updated_at = NOW() WHERE id = :id')
               ->execute([':id' => $planId]);
            echo json_encode(['success' => true, 'message' => 'Plan visibility toggled']);
            break;

        // ── Membership plan CRUD ──────────────────────────────────
        case 'create_membership':
            $p = sanitizeMembershipPlan($input);
            if (!$p['name']) {
                echo json_encode(['success' => false, 'message' => 'Plan name is required']);
                exit;
            }
            $stmt = $db->prepare(
                'INSERT INTO membership_plans
                   (name, description, price, duration_days, max_active_investments,
                    withdrawal_speed_hours, referral_commission_pct, priority_support,
                    has_analytics, has_strategy_reports, access_elite_plans, invitation_pools,
                    color_accent, badge_icon, sort_order, is_active)
                 VALUES
                   (:name, :desc, :price, :dur, :max_inv,
                    :wsh, :rcp, :ps,
                    :ha, :hsr, :aep, :ip,
                    :color, :badge, :sort, 1)'
            );
            $stmt->execute([
                ':name'   => $p['name'],   ':desc'   => $p['description'],
                ':price'  => $p['price'],  ':dur'    => $p['duration_days'],
                ':max_inv'=> $p['max_active_investments'],
                ':wsh'    => $p['withdrawal_speed_hours'],
                ':rcp'    => $p['referral_commission_pct'],
                ':ps'     => $p['priority_support'],
                ':ha'     => $p['has_analytics'],
                ':hsr'    => $p['has_strategy_reports'],
                ':aep'    => $p['access_elite_plans'],
                ':ip'     => $p['invitation_pools'],
                ':color'  => $p['color_accent'], ':badge' => $p['badge_icon'],
                ':sort'   => $p['sort_order'],
            ]);
            echo json_encode(['success' => true, 'message' => 'Membership plan created', 'data' => ['id' => (int)$db->lastInsertId()]]);
            break;

        case 'update_membership':
            $planId = (int) ($input['plan_id'] ?? 0);
            if (!$planId) { echo json_encode(['success' => false, 'message' => 'plan_id required']); exit; }
            $p = sanitizeMembershipPlan($input);
            $db->prepare(
                'UPDATE membership_plans SET
                   name = :name, description = :desc, price = :price,
                   duration_days = :dur, max_active_investments = :max_inv,
                   withdrawal_speed_hours = :wsh, referral_commission_pct = :rcp,
                   priority_support = :ps, has_analytics = :ha,
                   has_strategy_reports = :hsr, access_elite_plans = :aep,
                   invitation_pools = :ip, color_accent = :color,
                   badge_icon = :badge, sort_order = :sort,
                   updated_at = NOW()
                 WHERE id = :id'
            )->execute([
                ':name'   => $p['name'],   ':desc'   => $p['description'],
                ':price'  => $p['price'],  ':dur'    => $p['duration_days'],
                ':max_inv'=> $p['max_active_investments'],
                ':wsh'    => $p['withdrawal_speed_hours'],
                ':rcp'    => $p['referral_commission_pct'],
                ':ps'     => $p['priority_support'],
                ':ha'     => $p['has_analytics'],
                ':hsr'    => $p['has_strategy_reports'],
                ':aep'    => $p['access_elite_plans'],
                ':ip'     => $p['invitation_pools'],
                ':color'  => $p['color_accent'], ':badge' => $p['badge_icon'],
                ':sort'   => $p['sort_order'], ':id' => $planId,
            ]);
            echo json_encode(['success' => true, 'message' => 'Membership plan updated']);
            break;

        case 'delete_membership':
            $planId = (int) ($input['plan_id'] ?? 0);
            if (!$planId) { echo json_encode(['success' => false, 'message' => 'plan_id required']); exit; }
            $stmtCheck = $db->prepare('SELECT COUNT(*) FROM user_memberships WHERE plan_id = :id AND status = \'active\'');
            $stmtCheck->execute([':id' => $planId]);
            if ((int) $stmtCheck->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete plan with active members. Deactivate it instead.']);
                exit;
            }
            $db->prepare('DELETE FROM membership_plans WHERE id = :id')->execute([':id' => $planId]);
            echo json_encode(['success' => true, 'message' => 'Membership plan deleted']);
            break;

        case 'toggle_membership':
            $planId = (int) ($input['plan_id'] ?? 0);
            if (!$planId) { echo json_encode(['success' => false, 'message' => 'plan_id required']); exit; }
            $db->prepare('UPDATE membership_plans SET is_active = NOT is_active, updated_at = NOW() WHERE id = :id')
               ->execute([':id' => $planId]);
            echo json_encode(['success' => true, 'message' => 'Plan visibility toggled']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
