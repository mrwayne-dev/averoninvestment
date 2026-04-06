<?php
/* =====================================================================
   api/user-dashboard/update-settings.php
   Saves the user's notification preference toggles.
   Preferences are stored per-user in the site_settings table as a
   JSON blob under the key "user_notif_{user_id}".

   Method: POST
   Auth:   Session required (user_id)
   Body:   {
     notif_deposit_confirmed:    bool,
     notif_withdrawal_processed: bool,
     notif_profit_credited:      bool,
     notif_investment_completed: bool,
     notif_referral_earned:      bool,
     notif_security_alerts:      bool
   }
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// ── Parse + whitelist input ───────────────────────────────────────────
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$userId = (int) $_SESSION['user_id'];

$allowedKeys = [
    'notif_deposit_confirmed',
    'notif_withdrawal_processed',
    'notif_profit_credited',
    'notif_investment_completed',
    'notif_referral_earned',
    'notif_security_alerts',
];

$settings = [];
foreach ($allowedKeys as $key) {
    // Accept any value — cast to bool
    $settings[$key] = isset($input[$key]) ? (bool) $input[$key] : true;
}

try {
    $db = Database::getInstance()->getConnection();

    $settingKey = 'user_notif_' . $userId;

    // Upsert — insert if not exists, update value if key already present
    $stmt = $db->prepare(
        'INSERT INTO site_settings (setting_key, setting_value)
         VALUES (:key, :value)
         ON DUPLICATE KEY UPDATE setting_value = :value'
    );
    $stmt->execute([
        ':key'   => $settingKey,
        ':value' => json_encode($settings),
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Notification preferences saved.',
        'data'    => ['settings' => $settings],
    ]);

} catch (Throwable $e) {
    error_log('[update-settings] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save notification settings.']);
}
