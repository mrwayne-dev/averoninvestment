<?php
/* =====================================================================
   api/user-dashboard/record-trade.php
   Records a simulated TSLA buy/sell trade (display-only — no real
   securities or funds change hands).

   The trade is purely cosmetic:
     • Validates inputs (amount > 0, side is buy|sell)
     • Fetches latest TSLA price from tesla_stocks table
     • Inserts a notification for the user
     • Returns trade summary (shares estimate, price used)

   No wallet mutations. This is a paper-trading simulation feature only.

   Method: POST
   Auth:   Session required (user_id)
   Body:   { amount: float, side: "buy"|"sell" }
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

// ── Method + AJAX guards ─────────────────────────────────────────────
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

// ── Parse + validate input ────────────────────────────────────────────
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$amount = isset($input['amount']) ? (float) $input['amount'] : 0.0;
$side   = isset($input['side'])   ? strtolower(trim($input['side'])) : '';

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid amount.']);
    exit;
}

if (!in_array($side, ['buy', 'sell'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid trade side. Must be buy or sell.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // ── Fetch latest TSLA price ────────────────────────────────────────
    $stmtPrice = $db->prepare(
        'SELECT price, change_amount, change_percent
         FROM   tesla_stocks
         WHERE  symbol = :symbol
         ORDER  BY id DESC
         LIMIT  1'
    );
    $stmtPrice->execute([':symbol' => 'TSLA']);
    $stock = $stmtPrice->fetch();

    $tslaPrice = $stock ? (float) $stock['price'] : 250.00;
    $shares    = $tslaPrice > 0 ? round($amount / $tslaPrice, 6) : 0;

    // ── Insert notification (trade confirmation) ───────────────────────
    $sideLabel = $side === 'buy' ? 'Purchase' : 'Sale';
    $db->prepare(
        'INSERT INTO notifications (user_id, title, message, type)
         VALUES (:uid, :title, :message, :type)'
    )->execute([
        ':uid'     => $userId,
        ':title'   => 'Simulated TSLA ' . $sideLabel,
        ':message' => 'Paper trade recorded: '
                      . ($side === 'buy' ? 'Bought' : 'Sold') . ' '
                      . number_format($shares, 4) . ' TSLA shares'
                      . ' at $' . number_format($tslaPrice, 2)
                      . ' · Total: $' . number_format($amount, 2)
                      . '. (Simulation only — no real funds used.)',
        ':type'    => 'trade',
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Simulated ' . $side . ' order recorded.',
        'data'    => [
            'side'       => $side,
            'amount'     => number_format($amount, 2),
            'tsla_price' => number_format($tslaPrice, 2),
            'shares'     => number_format($shares, 4),
            'note'       => 'This is a paper trade simulation. No real funds are used.',
        ],
    ]);

} catch (Throwable $e) {
    error_log('[record-trade] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to record trade. Please try again.']);
}
