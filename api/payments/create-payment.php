<?php
/* =====================================================================
   api/payments/create-payment.php
   Initiates a crypto deposit via NowPayments hosted invoice.

   Flow:
     1. Validate amount (MIN_DEPOSIT ≤ amount ≤ MAX_DEPOSIT)
     2. Validate currency (must be in SUPPORTED_CRYPTOS)
     3. Call NowPayments Invoice API → get hosted invoice_url
     4. Insert transaction  (type=deposit, status=pending, reference=orderId)
     5. Insert nowpayments_orders row (nowpayments_payment_id = invoice id)
     6. Return { invoice_url } — frontend redirects user to NowPayments page

   Method: POST
   Auth:   Session required (user_id)
   Body:   { amount_usd: float, currency: string }
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';
require_once 'nowpayments.php';

// ── Auth guard ────────────────────────────────────────────────────────
requireAuth();

// ── Method + AJAX guards ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sendJsonResponse(false, 'Method not allowed');
}

if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    http_response_code(403);
    sendJsonResponse(false, 'Forbidden');
}

// ── Parse + sanitise input ────────────────────────────────────────────
$input     = json_decode(file_get_contents('php://input'), true) ?? [];
$amountUsd = isset($input['amount_usd']) ? (float) $input['amount_usd'] : 0.0;
$currency  = isset($input['currency'])   ? strtolower(trim($input['currency'])) : '';

// ── Validation ────────────────────────────────────────────────────────
if ($amountUsd < MIN_DEPOSIT) {
    sendJsonResponse(false, 'Minimum deposit amount is $' . number_format(MIN_DEPOSIT, 2) . '.');
}

if ($amountUsd > MAX_DEPOSIT) {
    sendJsonResponse(false, 'Maximum deposit amount is $' . number_format(MAX_DEPOSIT, 2) . '.');
}

$supportedCurrencies = array_map('strtolower', SUPPORTED_CRYPTOS);
if (!in_array($currency, $supportedCurrencies, true)) {
    sendJsonResponse(
        false,
        'Unsupported currency. Allowed: ' . implode(', ', SUPPORTED_CRYPTOS) . '.'
    );
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // ── 1. Fetch user info for records ───────────────────────────────
    $stmtUser = $db->prepare(
        'SELECT first_name, email FROM users WHERE id = :uid LIMIT 1'
    );
    $stmtUser->execute([':uid' => $userId]);
    $user = $stmtUser->fetch();

    if (!$user) {
        sendJsonResponse(false, 'User not found.');
    }

    // ── 2. Generate unique internal order ID ─────────────────────────
    //    This is what the NowPayments IPN sends back as order_id,
    //    and what we store in transactions.reference for lookup.
    $orderId = 'DEP-' . time() . '-' . $userId;

    // ── 3. Create NowPayments hosted invoice ─────────────────────────
    $baseUrl     = rtrim(APP_URL, '/');
    $callbackUrl = $baseUrl . '/api/webhooks/webhook.php';
    $successUrl  = $baseUrl . '/dashboard/wallet?deposit=success&order_id=' . urlencode($orderId);
    $cancelUrl   = $baseUrl . '/dashboard/wallet?deposit=cancelled';

    $np      = new NowPayments();
    $invoice = $np->createInvoice(
        $amountUsd,
        $currency,
        $orderId,
        $callbackUrl,
        $successUrl,
        $cancelUrl
    );

    $invoiceUrl = $invoice['invoice_url'] ?? '';
    // NowPayments invoice ID — stored as placeholder until the real
    // payment_id arrives via IPN webhook after the user actually pays.
    $invoiceId  = (string) ($invoice['id'] ?? $orderId);

    if (empty($invoiceUrl)) {
        sendJsonResponse(false, 'Failed to generate payment link. Please try again.');
    }

    // ── 4 & 5. Insert transaction + nowpayments_orders atomically ────
    $db->beginTransaction();

    $stmtTx = $db->prepare(
        'INSERT INTO transactions
           (user_id, type, amount, currency, status, reference, notes)
         VALUES
           (:uid, :type, :amount, :currency, :status, :ref, :notes)'
    );
    $stmtTx->execute([
        ':uid'      => $userId,
        ':type'     => 'deposit',
        ':amount'   => $amountUsd,
        ':currency' => strtoupper($currency),
        ':status'   => 'pending',
        ':ref'      => $orderId,           // Our order ID — matches IPN order_id
        ':notes'    => 'NowPayments invoice: ' . $invoiceId,
    ]);
    $transactionId = (int) $db->lastInsertId();

    // pay_address / pay_amount unknown until user chooses on NowPayments page;
    // placeholders stored so the row exists for the webhook to update.
    $stmtOrder = $db->prepare(
        'INSERT INTO nowpayments_orders
           (user_id, transaction_id, nowpayments_payment_id,
            pay_currency, pay_amount, pay_address,
            price_amount, price_currency, payment_status, qr_code_url)
         VALUES
           (:uid, :tx_id, :payment_id,
            :pay_currency, :pay_amount, :pay_address,
            :price_amount, :price_currency, :payment_status, :qr_url)'
    );
    $stmtOrder->execute([
        ':uid'            => $userId,
        ':tx_id'          => $transactionId,
        ':payment_id'     => $invoiceId,          // Invoice ID (updated by webhook to real payment_id)
        ':pay_currency'   => strtoupper($currency),
        ':pay_amount'     => 0,                   // Unknown until user selects on NowPayments
        ':pay_address'    => '',                  // Unknown until user selects on NowPayments
        ':price_amount'   => $amountUsd,
        ':price_currency' => 'USD',
        ':payment_status' => 'waiting',
        ':qr_url'         => '',
    ]);

    $db->commit();

    sendJsonResponse(true, 'Redirecting to checkout...', [
        'invoice_url' => $invoiceUrl,
        'order_id'    => $orderId,
    ]);

} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $errMsg = $e->getMessage();
    error_log('[create-payment] User ' . $userId . ': ' . $errMsg);
    // Expose real error in dev so it appears in the browser toast
    $displayMsg = (APP_ENV === 'development')
        ? 'NowPayments error: ' . $errMsg
        : 'Failed to create payment. Please try again.';
    sendJsonResponse(false, $displayMsg);
}
