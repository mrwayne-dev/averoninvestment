<?php
/* =====================================================================
   api/user-dashboard/start-investment.php
   Validates and creates a new user investment.

   Business rules enforced:
     1. User must have an active wallet with sufficient balance
        (balance >= investment amount)
     2. Amount must be within plan's min/max range
     3. User must not exceed membership's max_active_investments limit
     4. Plan must exist and be active
     5. Wallet balance is debited; invested_amount incremented
     6. Snapshot of daily yield rate stored at time of investment

   Method: POST
   Auth:   Session required (user_id)
   Body:   { plan_id: int, amount: float }
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

// ── Parse input ───────────────────────────────────────────────────────
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$planId = isset($input['plan_id']) ? (int) $input['plan_id'] : 0;
$amount = isset($input['amount'])  ? (float) $input['amount'] : 0.0;

if ($planId <= 0 || $amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid plan or amount.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // Fetch user info for email (outside transaction — read-only)
    $stmtUser = $db->prepare('SELECT first_name, email FROM users WHERE id = :uid LIMIT 1');
    $stmtUser->execute([':uid' => $userId]);
    $userRow = $stmtUser->fetch();

    $db->beginTransaction();

    // ── 1. Load and validate the investment plan ──────────────────────
    $stmtPlan = $db->prepare(
        'SELECT id, name, min_amount, max_amount,
                duration_days, daily_yield_min, daily_yield_max,
                profit_withdrawal_after_days, is_active
         FROM   investment_plans
         WHERE  id = :id
         LIMIT  1'
    );
    $stmtPlan->execute([':id' => $planId]);
    $plan = $stmtPlan->fetch();

    if (!$plan || !$plan['is_active']) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Investment plan not found or inactive.']);
        exit;
    }

    $minAmount = (float) $plan['min_amount'];
    $maxAmount = $plan['max_amount'] !== null ? (float) $plan['max_amount'] : PHP_FLOAT_MAX;

    if ($amount < $minAmount) {
        $db->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Amount is below the minimum of $' . number_format($minAmount, 2) . ' for this plan.',
        ]);
        exit;
    }

    if ($amount > $maxAmount) {
        $db->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Amount exceeds the maximum of $' . number_format($maxAmount, 2) . ' for this plan.',
        ]);
        exit;
    }

    // ── 2. Check membership investment limit ──────────────────────────
    $stmtMembership = $db->prepare(
        'SELECT mp.max_active_investments
         FROM   user_memberships  um
         JOIN   membership_plans  mp ON mp.id = um.plan_id
         WHERE  um.user_id  = :uid
           AND  um.status   = :status
           AND  um.end_date >= CURDATE()
         ORDER  BY um.end_date DESC
         LIMIT  1
         FOR UPDATE'
    );
    $stmtMembership->execute([':uid' => $userId, ':status' => 'active']);
    $membershipRow = $stmtMembership->fetch();
    $maxInvestments = $membershipRow
        ? ($membershipRow['max_active_investments'] !== null
            ? (int) $membershipRow['max_active_investments']
            : null)
        : 2; // Default: Basic Member limit

    if ($maxInvestments !== null) {
        $stmtActiveCount = $db->prepare(
            'SELECT COUNT(*)
             FROM   user_investments
             WHERE  user_id = :uid AND status = :status'
        );
        $stmtActiveCount->execute([':uid' => $userId, ':status' => 'active']);
        $activeCount = (int) $stmtActiveCount->fetchColumn();

        if ($activeCount >= $maxInvestments) {
            $db->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'You have reached your membership investment limit. Upgrade to start more.',
            ]);
            exit;
        }
    }

    // ── 3. Check and debit wallet balance ─────────────────────────────
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
            'message' => 'Insufficient wallet balance. Please deposit funds first.',
        ]);
        exit;
    }

    // Debit balance, increment invested_amount
    // PDO does not allow the same named placeholder twice in one statement,
    // so use :debit and :invest for the same value.
    $stmtDebit = $db->prepare(
        'UPDATE wallets
         SET    balance         = balance         - :debit,
                invested_amount = invested_amount + :invest
         WHERE  user_id = :uid'
    );
    $stmtDebit->execute([':debit' => $amount, ':invest' => $amount, ':uid' => $userId]);

    // ── 4. Snapshot daily yield rate (midpoint) ───────────────────────
    $dailyYieldRate = ((float) $plan['daily_yield_min'] + (float) $plan['daily_yield_max']) / 2;

    // ── 5. Compute dates ──────────────────────────────────────────────
    $today               = new DateTimeImmutable('today', new DateTimeZone('UTC'));
    $durationDays        = (int) $plan['duration_days'];
    $profitAfterDays     = (int) $plan['profit_withdrawal_after_days'];

    $startDate           = $today->format('Y-m-d');
    $endDate             = $today->modify("+{$durationDays} days")->format('Y-m-d');
    $profitAvailableDate = $today->modify("+{$profitAfterDays} days")->format('Y-m-d');

    // ── 6. Insert user_investments row ────────────────────────────────
    $stmtInvest = $db->prepare(
        'INSERT INTO user_investments
           (user_id, plan_id, amount, daily_yield_rate,
            profit_earned, status,
            profit_available_date, start_date, end_date)
         VALUES
           (:uid, :plan_id, :amount, :daily_rate,
            0.00, :status,
            :profit_date, :start, :end)'
    );
    $stmtInvest->execute([
        ':uid'         => $userId,
        ':plan_id'     => $planId,
        ':amount'      => $amount,
        ':daily_rate'  => $dailyYieldRate,
        ':status'      => 'active',
        ':profit_date' => $profitAvailableDate,
        ':start'       => $startDate,
        ':end'         => $endDate,
    ]);
    $investmentId = $db->lastInsertId();

    // ── 7. Record transaction (type = profit tracks daily cron;
    //       we log the initial investment debit here as a note only) ──
    // No transaction row needed for the debit itself —
    // the wallet UPDATE is the canonical record.
    // Profit credit rows are inserted by the cron job.

    // ── 8. Insert notification ────────────────────────────────────────
    $db->prepare(
        'INSERT INTO notifications (user_id, title, message, type)
         VALUES (:uid, :title, :message, :type)'
    )->execute([
        ':uid'     => $userId,
        ':title'   => 'Investment Started',
        ':message' => 'Your $' . number_format($amount, 2)
                      . ' investment in the ' . $plan['name']
                      . ' has been activated. Expected maturity: ' . $endDate . '.',
        ':type'    => 'investment',
    ]);

    $db->commit();

    // Send investment-started email (non-blocking — never fail the request on SMTP error)
    if ($userRow) {
        try {
            emailInvestmentStarted(
                $userRow['email'],
                $userRow['first_name'],
                $plan['name'],
                number_format($amount, 2),
                $startDate,
                $endDate,
                $profitAvailableDate,
                number_format($dailyYieldRate, 2) . '%'
            );
        } catch (Throwable $emailErr) {
            error_log('[start-investment] Email failed for user ' . $userId . ': ' . $emailErr->getMessage());
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Investment started successfully.',
        'data'    => [
            'investment_id' => (int) $investmentId,
            'plan_name'     => $plan['name'],
            'amount'        => number_format($amount, 2),
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'profit_date'   => $profitAvailableDate,
        ],
    ]);

} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('[start-investment] User ' . ($userId ?? '?') . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    $devMsg = (defined('APP_ENV') && APP_ENV === 'development')
        ? '[DEV] ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'
        : 'Failed to start investment. Please try again.';
    echo json_encode(['success' => false, 'message' => $devMsg]);
}
