<?php
/* =====================================================================
   api/user-dashboard/get-profile.php
   Returns the authenticated user's full profile data, active
   membership info, and notification preferences.

   Method: GET
   Auth:   Session required (user_id)

   Response data:
     first_name, last_name, email, country, language,
     referral_code, member_since,
     membership_tier, membership_expiry,
     notif_settings  – JSON object with boolean toggles
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

    // ── 1. Fetch user row ──────────────────────────────────────────────
    $stmtUser = $db->prepare(
        'SELECT first_name, last_name, email,
                region, language, created_at
         FROM   users
         WHERE  id = :uid
         LIMIT  1'
    );
    $stmtUser->execute([':uid' => $userId]);
    $user = $stmtUser->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    // ── 2. Active membership ───────────────────────────────────────────
    $stmtMembership = $db->prepare(
        'SELECT mp.name AS tier_name, um.end_date
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

    // ── 3. Notification preferences (stored per-user in site_settings) ─
    $stmtNotif = $db->prepare(
        "SELECT setting_value
         FROM   site_settings
         WHERE  setting_key = :key
         LIMIT  1"
    );
    $stmtNotif->execute([':key' => 'user_notif_' . $userId]);
    $rawNotif = $stmtNotif->fetchColumn();
    $savedNotif = $rawNotif ? json_decode($rawNotif, true) : [];

    $defaultNotif = [
        'notif_deposit_confirmed'    => true,
        'notif_withdrawal_processed' => true,
        'notif_profit_credited'      => true,
        'notif_investment_completed' => true,
        'notif_security_alerts'      => true,
    ];
    $notifSettings = array_merge($defaultNotif, (array) $savedNotif);

    // ── Response ───────────────────────────────────────────────────────
    echo json_encode([
        'success' => true,
        'data'    => [
            'first_name'         => $user['first_name'],
            'last_name'          => $user['last_name'],
            'email'              => $user['email'],
            'country'            => $user['region']   ?? '',
            'language'           => $user['language'] ?? '',
            'member_since'       => $user['created_at'],
            'membership_tier'    => $membership ? $membership['tier_name'] : null,
            'membership_expiry'  => $membership ? $membership['end_date']  : null,
            'notif_settings'     => $notifSettings,
        ],
    ]);

} catch (Throwable $e) {
    error_log('[get-profile] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load profile.']);
}
