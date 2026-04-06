<?php
/* =====================================================================
   api/user-dashboard/enroll-membership.php
   Enrolls the authenticated user in a membership plan.

   Business rules:
     1. Plan must exist and be active
     2. User's wallet balance >= plan price
     3. Any existing active membership is expired first (upgrade/downgrade)
     4. Wallet balance debited
     5. Transaction row inserted (type = membership_fee)
     6. user_memberships row inserted
     7. Notification inserted

   Method: POST
   Auth:   Session required (user_id)
   Body:   { plan_id: int }
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

if ($planId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid membership plan.']);
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

    // ── 1. Load and validate the membership plan ──────────────────────
    $stmtPlan = $db->prepare(
        'SELECT id, name, price, duration_days,
                referral_commission_pct, is_active
         FROM   membership_plans
         WHERE  id = :id
         LIMIT  1'
    );
    $stmtPlan->execute([':id' => $planId]);
    $plan = $stmtPlan->fetch();

    if (!$plan || !$plan['is_active']) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Membership plan not found or inactive.']);
        exit;
    }

    $price        = (float) $plan['price'];
    $durationDays = (int)   $plan['duration_days'];

    // ── 2. Check wallet balance ───────────────────────────────────────
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

    if ((float) $wallet['balance'] < $price) {
        $db->rollBack();
        $shortfall = $price - (float) $wallet['balance'];
        echo json_encode([
            'success' => false,
            'message' => 'Insufficient balance. You need $' . number_format($shortfall, 2) . ' more.',
        ]);
        exit;
    }

    // ── 3. Expire any existing active memberships ──────────────────────
    $db->prepare(
        'UPDATE user_memberships
         SET    status = :expired
         WHERE  user_id = :uid AND status = :active'
    )->execute([':expired' => 'expired', ':uid' => $userId, ':active' => 'active']);

    // ── 4. Debit wallet ───────────────────────────────────────────────
    $db->prepare(
        'UPDATE wallets
         SET    balance = balance - :price
         WHERE  user_id = :uid'
    )->execute([':price' => $price, ':uid' => $userId]);

    // ── 5. Insert membership_fee transaction ──────────────────────────
    $ref = 'MF-' . strtoupper(bin2hex(random_bytes(4)));

    $db->prepare(
        'INSERT INTO transactions
           (user_id, type, amount, currency, status, reference, notes, processed_at)
         VALUES
           (:uid, :type, :amount, :currency, :status, :ref, :notes, NOW())'
    )->execute([
        ':uid'      => $userId,
        ':type'     => 'membership_fee',
        ':amount'   => $price,
        ':currency' => 'USD',
        ':status'   => 'confirmed',
        ':ref'      => $ref,
        ':notes'    => $plan['name'] . ' — ' . $durationDays . '-day membership',
    ]);

    // ── 6. Insert user_memberships row ────────────────────────────────
    $today    = new DateTimeImmutable('today', new DateTimeZone('UTC'));
    $endDate  = $today->modify("+{$durationDays} days")->format('Y-m-d');
    $startDate = $today->format('Y-m-d');

    $db->prepare(
        'INSERT INTO user_memberships
           (user_id, plan_id, commission_rate, status, start_date, end_date)
         VALUES
           (:uid, :plan_id, :commission, :status, :start, :end)'
    )->execute([
        ':uid'        => $userId,
        ':plan_id'    => $planId,
        ':commission' => $plan['referral_commission_pct'],
        ':status'     => 'active',
        ':start'      => $startDate,
        ':end'        => $endDate,
    ]);

    // ── 7. Update referral commission_rate if user was referred ───────
    // The referrer's commission on future deposits uses the enrollee's current rate.
    // We update the referrals row so the cron/webhook uses the fresh rate.
    $db->prepare(
        'UPDATE referrals
         SET    commission_rate = :rate
         WHERE  referred_id = :uid'
    )->execute([':rate' => $plan['referral_commission_pct'], ':uid' => $userId]);

    // ── 8. Insert notification ────────────────────────────────────────
    $db->prepare(
        'INSERT INTO notifications (user_id, title, message, type)
         VALUES (:uid, :title, :message, :type)'
    )->execute([
        ':uid'     => $userId,
        ':title'   => 'Membership Activated',
        ':message' => 'Your ' . $plan['name'] . ' has been activated. '
                      . 'Valid until ' . $endDate . '. Reference: ' . $ref . '.',
        ':type'    => 'membership',
    ]);

    $db->commit();

    // Send membership-enrolled email (non-blocking — never fail the request on SMTP error)
    if ($userRow) {
        try {
            emailMembershipEnrolled(
                $userRow['email'],
                $userRow['first_name'],
                $plan['name'],
                number_format($price, 2),
                $startDate,
                $endDate,
                $ref
            );
        } catch (Throwable $emailErr) {
            error_log('[enroll-membership] Email failed for user ' . $userId . ': ' . $emailErr->getMessage());
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $plan['name'] . ' activated successfully.',
        'data'    => [
            'plan_name'  => $plan['name'],
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'price'      => number_format($price, 2),
            'reference'  => $ref,
        ],
    ]);

} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('[enroll-membership] User ' . $userId . ': ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to enroll in membership. Please try again.']);
}
