<?php
/* =====================================================================
   api/user-dashboard/create-withdrawal.php
   Creates a pending withdrawal request.

   Business rules:
     1. Amount >= MIN_WITHDRAWAL ($50)
     2. Amount <= user's available wallet balance
     3. 1.5% platform fee deducted — user receives net amount
     4. Wallet balance debited immediately (funds reserved)
     5. Transaction row created with status = 'pending'
     6. Notification inserted
     7. Processing speed label derived from membership tier

   Method: POST
   Auth:   Session required (user_id)
   Body:   { amount: float, currency: string, wallet_address: string }
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/email-templates.php';

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

// ── Parse + sanitise input ────────────────────────────────────────────
$input         = json_decode(file_get_contents('php://input'), true) ?? [];
$amount        = isset($input['amount'])         ? (float)  $input['amount']         : 0.0;
$currency      = isset($input['currency'])       ? strtoupper(trim($input['currency'])) : '';
$walletAddress = isset($input['wallet_address']) ? trim($input['wallet_address'])     : '';

// ── Basic validation ──────────────────────────────────────────────────
if ($amount < MIN_WITHDRAWAL) {
    echo json_encode([
        'success' => false,
        'message' => 'Minimum withdrawal amount is $' . number_format(MIN_WITHDRAWAL, 2) . '.',
    ]);
    exit;
}

$allowedCurrencies = ['BTC', 'ETH', 'USDTTRC20', 'USDTERC20'];
if (!in_array($currency, $allowedCurrencies, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid withdrawal currency.']);
    exit;
}

if (strlen($walletAddress) < 10) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid wallet address.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // Fetch user info for email (outside transaction — read-only)
    $stmtUser = $db->prepare('SELECT first_name, email FROM users WHERE id = :uid LIMIT 1');
    $stmtUser->execute([':uid' => $userId]);
    $userRow = $stmtUser->fetch();

    // ── 0. Verify user has at least one investment ────────────────────
    $stmtInvCheck = $db->prepare(
        "SELECT COUNT(*) FROM user_investments
         WHERE user_id = :uid
           AND status IN ('active', 'completed', 'matured')"
    );
    $stmtInvCheck->execute([':uid' => $userId]);
    if ((int) $stmtInvCheck->fetchColumn() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'You must have an active investment to withdraw funds. Please invest first.',
        ]);
        exit;
    }

    $db->beginTransaction();

    // ── 1. Load and lock wallet ───────────────────────────────────────
    $stmtWallet = $db->prepare(
        'SELECT id, balance
         FROM   wallets
         WHERE  user_id = :uid
         LIMIT  1
         FOR UPDATE'
    );
    $stmtWallet->execute([':uid' => $userId]);
    $wallet = $stmtWallet->fetch();

    if (!$wallet) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Wallet not found.']);
        exit;
    }

    if ((float) $wallet['balance'] < $amount) {
        $db->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Insufficient balance. Available: $' . number_format((float) $wallet['balance'], 2) . '.',
        ]);
        exit;
    }

    // ── 2. Compute fee and net amount ─────────────────────────────────
    $feePct    = WITHDRAWAL_FEE_PCT / 100;         // 0.015
    $feeAmount = round($amount * $feePct, 2);
    $netAmount = round($amount - $feeAmount, 2);

    // ── 3. Determine processing speed label from active membership ────
    $stmtMembership = $db->prepare(
        'SELECT mp.withdrawal_speed_hours
         FROM   user_memberships  um
         JOIN   membership_plans  mp ON mp.id = um.plan_id
         WHERE  um.user_id  = :uid
           AND  um.status   = :status
           AND  um.end_date >= CURDATE()
         ORDER  BY um.end_date DESC
         LIMIT  1'
    );
    $stmtMembership->execute([':uid' => $userId, ':status' => 'active']);
    $membershipRow    = $stmtMembership->fetch();
    $speedHours       = $membershipRow ? (int) $membershipRow['withdrawal_speed_hours'] : 72;
    $speedLabel       = $speedHours === 1
        ? 'Within 1 hour'
        : 'Up to ' . $speedHours . ' hours';

    // ── 4. Debit wallet balance ───────────────────────────────────────
    $db->prepare(
        'UPDATE wallets
         SET    balance = balance - :amount
         WHERE  user_id = :uid'
    )->execute([':amount' => $amount, ':uid' => $userId]);

    // ── 5. Insert transactions: withdrawal + fee rows ─────────────────
    //   Row 1 — the full withdrawal (gross)
    $ref = 'WD-' . strtoupper(bin2hex(random_bytes(4)));

    $stmtTx = $db->prepare(
        'INSERT INTO transactions
           (user_id, type, amount, currency, status, reference, notes)
         VALUES
           (:uid, :type, :amount, :currency, :status, :ref, :notes)'
    );
    $stmtTx->execute([
        ':uid'      => $userId,
        ':type'     => 'withdrawal',
        ':amount'   => $amount,
        ':currency' => $currency,
        ':status'   => 'pending',
        ':ref'      => $ref,
        ':notes'    => 'To: ' . $walletAddress . ' · Fee: $' . $feeAmount . ' · Net: $' . $netAmount,
    ]);
    $txId = $db->lastInsertId();

    //   Row 2 — the platform fee (separate line item)
    $stmtFee = $db->prepare(
        'INSERT INTO transactions
           (user_id, type, amount, currency, status, reference, notes)
         VALUES
           (:uid, :type, :amount, :currency, :status, :ref, :notes)'
    );
    $stmtFee->execute([
        ':uid'      => $userId,
        ':type'     => 'fee',
        ':amount'   => $feeAmount,
        ':currency' => 'USD',
        ':status'   => 'confirmed',
        ':ref'      => $ref . '-FEE',
        ':notes'    => '1.5% withdrawal fee on ' . $ref,
    ]);

    // ── 6. Insert notification ────────────────────────────────────────
    $db->prepare(
        'INSERT INTO notifications (user_id, title, message, type)
         VALUES (:uid, :title, :message, :type)'
    )->execute([
        ':uid'     => $userId,
        ':title'   => 'Withdrawal Requested',
        ':message' => 'Your withdrawal of $' . number_format($amount, 2)
                      . ' (' . $currency . ') has been submitted. '
                      . 'Processing time: ' . $speedLabel . '. Reference: ' . $ref . '.',
        ':type'    => 'withdrawal',
    ]);

    $db->commit();

    // Send withdrawal-pending email (non-blocking — failure does not affect response)
    if ($userRow) {
        emailWithdrawalPending(
            $userRow['email'],
            $userRow['first_name'],
            number_format($amount, 2),
            $currency,
            number_format($netAmount, 2),
            number_format($feeAmount, 2),
            $ref,
            $speedLabel
        );
    }

    echo json_encode([
        'success' => true,
        'message' => 'Withdrawal request submitted. ' . $speedLabel . '.',
        'data'    => [
            'transaction_id' => (int) $txId,
            'reference'      => $ref,
            'amount'         => number_format($amount, 2),
            'fee'            => number_format($feeAmount, 2),
            'net_amount'     => number_format($netAmount, 2),
            'currency'       => $currency,
            'speed_label'    => $speedLabel,
        ],
    ]);

} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('[create-withdrawal] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit withdrawal. Please try again.']);
}
