<?php
/* =====================================================================
   api/user-dashboard/get-referral-stats.php
   Returns the user's referral code, referral URL, summary stats,
   and list of referred users.

   Method: GET
   Auth:   Session required (user_id)

   Response data:
     referral_code   – user's unique code
     referral_url    – full sign-up URL with ?ref= param
     total_referred  – number of users signed up via this link
     total_earned    – total commission USD earned (formatted)
     commission_rate – current rate from active membership
     referrals[]     – recent 20 individual referral rows
       each: id, first_name, last_name, commission_rate, total_earned, created_at
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';

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

    // ── 1. Fetch user's referral code ─────────────────────────────────
    $stmtUser = $db->prepare(
        'SELECT referral_code FROM users WHERE id = :uid LIMIT 1'
    );
    $stmtUser->execute([':uid' => $userId]);
    $user = $stmtUser->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    // Provision referral code if missing (lazy generation)
    $referralCode = $user['referral_code'];
    if (empty($referralCode)) {
        $referralCode = generateReferralCode($userId);
        $db->prepare('UPDATE users SET referral_code = :code WHERE id = :uid')
           ->execute([':code' => $referralCode, ':uid' => $userId]);
    }

    // ── 2. Summary stats (total referred + total earned) ─────────────
    $stmtSummary = $db->prepare(
        'SELECT COUNT(*)                    AS total_referred,
                COALESCE(SUM(total_earned), 0) AS total_earned
         FROM   referrals
         WHERE  referrer_id = :uid'
    );
    $stmtSummary->execute([':uid' => $userId]);
    $summary = $stmtSummary->fetch();

    // ── 3. Current commission rate from active membership ─────────────
    $currentRate = getReferralCommissionRate($userId, $db);

    // ── 4. Individual referral rows (latest 20) ───────────────────────
    $stmtReferrals = $db->prepare(
        'SELECT r.id,
                r.commission_rate,
                r.total_earned,
                r.created_at,
                u.first_name,
                u.last_name
         FROM   referrals r
         JOIN   users     u ON u.id = r.referred_id
         WHERE  r.referrer_id = :uid
         ORDER  BY r.created_at DESC
         LIMIT  20'
    );
    $stmtReferrals->execute([':uid' => $userId]);
    $referrals = $stmtReferrals->fetchAll();

    // Cast types
    $referrals = array_map(static function (array $r): array {
        $r['commission_rate'] = (float) $r['commission_rate'];
        $r['total_earned']    = (float) $r['total_earned'];
        // Mask last name to first letter for privacy display
        $r['last_name_initial'] = $r['last_name'] ? strtoupper($r['last_name'][0]) . '.' : '';
        return $r;
    }, $referrals);

    // ── 5. Build referral URL ─────────────────────────────────────────
    $referralUrl = APP_URL . '/register?ref=' . urlencode($referralCode);

    echo json_encode([
        'success' => true,
        'data'    => [
            'referral_code'   => $referralCode,
            'referral_url'    => $referralUrl,
            'total_referred'  => (int) $summary['total_referred'],
            'total_earned'    => number_format((float) $summary['total_earned'], 2),
            'commission_rate' => $currentRate,
            'referrals'       => $referrals,
        ],
    ]);

} catch (Throwable $e) {
    error_log('[get-referral-stats] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load referral stats.']);
}
