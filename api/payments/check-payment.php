<?php
/* =====================================================================
   api/payments/check-payment.php
   Returns the current status of a deposit by looking up our DB.

   In the NowPayments invoice flow the user is redirected to NowPayments
   and returned via success_url. This endpoint is polled after the user
   returns so the wallet page can confirm the balance was credited.

   If the NowPayments order is already marked confirmed/finished in the
   DB (e.g. webhook already fired, or dev team updated it manually) but
   the transaction is still pending, this endpoint will call
   confirmDeposit() itself — acting as a webhook fallback.

   The NowPayments webhook (webhook.php) is the primary confirmation
   mechanism; this endpoint is the safety net.

   Method: GET
   Auth:   Session required (user_id)
   Params: ?order_id={string}   — our internal DEP-{time}-{uid} reference
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';
require_once 'payment-helper.php';

// ── Auth guard ────────────────────────────────────────────────────────
requireAuth();

// ── Method + AJAX guards ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    sendJsonResponse(false, 'Method not allowed');
}

if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    sendJsonResponse(false, 'Forbidden');
}

// ── Input ─────────────────────────────────────────────────────────────
$orderId = trim($_GET['order_id'] ?? '');
if ($orderId === '') {
    sendJsonResponse(false, 'order_id is required.');
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // Look up by our internal order_id (transactions.reference)
    // User_id guard prevents IDOR
    $stmt = $db->prepare(
        'SELECT t.id           AS tx_id,
                t.status       AS tx_status,
                t.amount       AS tx_amount,
                t.currency     AS tx_currency,
                t.reference    AS tx_reference,
                npo.id         AS order_row_id,
                npo.payment_status,
                npo.nowpayments_payment_id,
                u.first_name,
                u.email
         FROM   transactions       t
         JOIN   nowpayments_orders npo ON npo.transaction_id = t.id
         JOIN   users              u   ON u.id = t.user_id
         WHERE  t.reference = :order_id
           AND  t.user_id   = :uid
         LIMIT  1'
    );
    $stmt->execute([':order_id' => $orderId, ':uid' => $userId]);
    $row = $stmt->fetch();

    if (!$row) {
        sendJsonResponse(false, 'Payment not found.');
    }

    // ── Webhook fallback: NowPayments confirmed but tx still pending ──
    // This covers: local dev (webhook can't reach localhost), or the
    // rare race where the IPN arrived after the user was redirected back.
    $npStatus = strtolower($row['payment_status'] ?? '');
    $txStatus = $row['tx_status'];

    if (
        $txStatus === 'pending' &&
        in_array($npStatus, ['confirmed', 'finished'], true)
    ) {
        // confirmDeposit() is idempotent — safe to call even if webhook
        // already fired (the FOR UPDATE lock prevents double-credit).
        confirmDeposit(
            $db,
            (int)   $row['tx_id'],
            $userId,
            (float) $row['tx_amount'],
            (string) $row['first_name'],
            (string) $row['email'],
            (string) $row['tx_currency'],
            (string) ($row['nowpayments_payment_id'] ?: $orderId)
        );

        // Re-read tx_status after confirmation
        $txStatus = 'confirmed';
    }

    $confirmed = $txStatus === 'confirmed';

    sendJsonResponse(true, 'Status retrieved.', [
        'status'    => $confirmed ? 'confirmed' : ($npStatus ?: 'waiting'),
        'confirmed' => $confirmed,
    ]);

} catch (Throwable $e) {
    error_log('[check-payment] User ' . $userId . ': ' . $e->getMessage());
    sendJsonResponse(false, 'Failed to check payment status. Please try again.');
}
