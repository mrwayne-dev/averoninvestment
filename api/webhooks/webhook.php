<?php
/* =====================================================================
   api/webhooks/webhook.php
   NowPayments IPN (Instant Payment Notification) handler.

   Security model:
     • No session / auth required — called by NowPayments servers
     • HMAC-SHA512 signature verified before ANY data is processed
     • Confirmation logic is idempotent (double-delivery is safe)
     • Always responds HTTP 200 / "OK" so NowPayments stops retrying

   Invoice flow — IPN payload contains:
     • order_id    → our internal order ID (DEP-{time}-{userId})
                     stored in transactions.reference at payment creation
     • payment_id  → the real NowPayments payment ID assigned when the
                     user actually pays; used to update our order row
     • payment_status → confirmed | finished | waiting | expired etc.

   Flow:
     1. Read raw payload + signature header
     2. Verify HMAC — reject with 400 if invalid
     3. Parse JSON payload
     4. If payment_status = confirmed | finished:
          a. Look up transaction by order_id (transactions.reference)
          b. Update nowpayments_orders.nowpayments_payment_id → real payment_id
          c. Call confirmDeposit()
     5. Echo "OK" with HTTP 200 in all cases
   ===================================================================== */

// No JSON header — webhook responds with plain text "OK"
header('X-Content-Type-Options: nosniff');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';
require_once '../payments/nowpayments.php';
require_once '../payments/payment-helper.php';

// ── 1. Read raw payload and signature ────────────────────────────────
$rawPayload = file_get_contents('php://input');
$signature  = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'] ?? '';

// ── 2. Verify HMAC signature ──────────────────────────────────────────
if (!NowPayments::verifyIPN($rawPayload, $signature)) {
    error_log('[webhook] Invalid IPN signature. IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    http_response_code(400);
    echo 'Invalid signature';
    exit;
}

// ── 3. Parse JSON payload ─────────────────────────────────────────────
$data = json_decode($rawPayload, true);

if (!is_array($data)) {
    error_log('[webhook] Non-JSON payload received.');
    http_response_code(400);
    echo 'Bad payload';
    exit;
}

// Extract relevant fields from the IPN
$paymentStatus = strtolower($data['payment_status'] ?? '');
$paymentId     = (string) ($data['payment_id']     ?? ''); // Real NowPayments payment ID
$orderId       = (string) ($data['order_id']       ?? ''); // Our internal DEP-{time}-{userId}

error_log(
    '[webhook] IPN received.'
    . ' order_id='     . $orderId
    . ' payment_id='   . $paymentId
    . ' status='       . $paymentStatus
);

// ── 4. Only process confirmed / finished payments ─────────────────────
$confirmedStatuses = ['confirmed', 'finished'];

if (!in_array($paymentStatus, $confirmedStatuses, true)) {
    // Log other statuses (waiting, partially_paid, expired, etc.) but take no action
    http_response_code(200);
    echo 'OK';
    exit;
}

if (empty($orderId)) {
    error_log('[webhook] Missing order_id in IPN payload.');
    http_response_code(200);
    echo 'OK';
    exit;
}

// ── 5. Process payment confirmation ──────────────────────────────────
try {
    $db = Database::getInstance()->getConnection();

    // Look up transaction by our internal order_id (stored in transactions.reference)
    // and join to get user info and the nowpayments_orders row.
    $stmtOrder = $db->prepare(
        'SELECT t.id            AS transaction_id,
                t.status        AS tx_status,
                t.amount        AS tx_amount,
                t.user_id,
                npo.id          AS order_row_id,
                npo.pay_currency,
                u.first_name,
                u.email
         FROM   transactions       t
         JOIN   users              u   ON u.id  = t.user_id
         JOIN   nowpayments_orders npo ON npo.transaction_id = t.id
         WHERE  t.reference = :order_id
         LIMIT  1'
    );
    $stmtOrder->execute([':order_id' => $orderId]);
    $order = $stmtOrder->fetch();

    if (!$order) {
        error_log('[webhook] No transaction found for order_id: ' . $orderId);
        http_response_code(200);
        echo 'OK';
        exit;
    }

    // Update the nowpayments_orders row with the real payment_id and status
    $db->prepare(
        'UPDATE nowpayments_orders
         SET    payment_status          = :status,
                nowpayments_payment_id  = :pid
         WHERE  id = :row_id'
    )->execute([
        ':status'  => $paymentStatus,
        ':pid'     => $paymentId ?: $orderId,   // fall back to orderId if payment_id empty
        ':row_id'  => $order['order_row_id'],
    ]);

    // Idempotency: skip if already confirmed
    if ($order['tx_status'] !== 'pending') {
        error_log(
            '[webhook] Already processed. order_id=' . $orderId
            . ' tx_status=' . $order['tx_status']
        );
        http_response_code(200);
        echo 'OK';
        exit;
    }

    // Confirm the deposit — credits wallet, referral commission, email
    $confirmed = confirmDeposit(
        $db,
        (int)    $order['transaction_id'],
        (int)    $order['user_id'],
        (float)  $order['tx_amount'],
               $order['first_name'],
               $order['email'],
        strtoupper($order['pay_currency']),
        $paymentId ?: $orderId
    );

    error_log(
        '[webhook] Deposit '
        . ($confirmed ? 'confirmed' : 'skipped (already processed)')
        . '. order_id='  . $orderId
        . ' payment_id=' . $paymentId
        . ' user_id='    . $order['user_id']
        . ' amount='     . $order['tx_amount']
    );

} catch (Throwable $e) {
    error_log('[webhook] Error processing order_id=' . $orderId . ': ' . $e->getMessage());
    // Still respond 200 so NowPayments does not infinitely retry
}

// ── 6. Always respond HTTP 200 ────────────────────────────────────────
http_response_code(200);
echo 'OK';
