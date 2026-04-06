<?php
/* =====================================================================
   api/user-dashboard/transfer.php
   Internal wallet-to-wallet transfer between platform users.

   Method: POST
   Auth:   requireAuth()
   Input:  { recipient_email: string, amount: float }

   Validation:
     • recipient user must exist and be active
     • sender != recipient
     • amount > 0
     • sender wallet balance >= amount

   Process (wrapped in DB transaction):
     1. Deduct amount from sender's wallet.balance
     2. Credit amount to recipient's wallet.balance
     3. Insert transactions record (sender)  — type = transfer_sent
     4. Insert transactions record (recipient) — type = transfer_received
     5. Insert notifications for both users
     6. Send email confirmations

   Returns: JSON { success, message }
   ===================================================================== */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../../config/database.php';
require_once '../../config/constants.php';
require_once '../utilities/helper.php';
require_once '../utilities/email-templates.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Method not allowed');
}

// ── CSRF validation ───────────────────────────────────────────
validateCsrfToken();

// ── Parse + sanitize input ────────────────────────────────────
$input           = json_decode(file_get_contents('php://input'), true) ?? [];
$recipientEmail  = trim(strtolower($input['recipient_email'] ?? ''));
$amount          = isset($input['amount']) ? round((float) $input['amount'], 2) : 0.0;

// ── Basic field validation ────────────────────────────────────
if (empty($recipientEmail)) {
    sendJsonResponse(false, 'Recipient email is required.');
}
if (!isValidEmail($recipientEmail)) {
    sendJsonResponse(false, 'Invalid recipient email address.');
}
if ($amount < 1.00) {
    sendJsonResponse(false, 'Minimum transfer amount is $1.00.');
}

$senderId = (int) $_SESSION['user_id'];

try {
    $db = Database::getInstance()->getConnection();

    // ── Fetch sender ──────────────────────────────────────────
    $stmtSender = $db->prepare("
        SELECT u.id, u.first_name, u.last_name, u.email,
               w.balance
        FROM   users   u
        JOIN   wallets w ON w.user_id = u.id
        WHERE  u.id = :id AND u.status = 'active'
        LIMIT  1
    ");
    $stmtSender->execute([':id' => $senderId]);
    $sender = $stmtSender->fetch();

    if (!$sender) {
        sendJsonResponse(false, 'Sender account not found.');
    }

    // ── Prevent self-transfer ─────────────────────────────────
    if (strtolower($sender['email']) === $recipientEmail) {
        sendJsonResponse(false, 'You cannot transfer funds to yourself.');
    }

    // ── Fetch recipient ───────────────────────────────────────
    $stmtRecip = $db->prepare("
        SELECT u.id, u.first_name, u.email
        FROM   users u
        WHERE  LOWER(u.email) = :email AND u.status = 'active'
        LIMIT  1
    ");
    $stmtRecip->execute([':email' => $recipientEmail]);
    $recipient = $stmtRecip->fetch();

    if (!$recipient) {
        sendJsonResponse(false, 'No active account found with that email address.');
    }

    // ── Balance check ─────────────────────────────────────────
    $senderBalance = (float) $sender['balance'];
    if ($senderBalance < $amount) {
        sendJsonResponse(false, sprintf(
            'Insufficient balance. You have %s available.',
            formatMoney($senderBalance)
        ));
    }

    // ── DB transaction ────────────────────────────────────────
    $db->beginTransaction();

    try {
        $now = date('Y-m-d H:i:s');
        $ref = 'TRF-' . strtoupper(bin2hex(random_bytes(5)));

        // 1. Deduct from sender
        $db->prepare("
            UPDATE wallets
            SET    balance = balance - :amount
            WHERE  user_id = :uid
        ")->execute([':amount' => $amount, ':uid' => $senderId]);

        // 2. Credit to recipient
        $db->prepare("
            UPDATE wallets
            SET    balance = balance + :amount
            WHERE  user_id = :uid
        ")->execute([':amount' => $amount, ':uid' => $recipient['id']]);

        // 3. Sender transaction record
        $db->prepare("
            INSERT INTO transactions
                   (user_id, type, amount, status, reference, notes, created_at)
            VALUES (:uid, 'transfer_sent', :amount, 'completed', :ref, :notes, :now)
        ")->execute([
            ':uid'    => $senderId,
            ':amount' => $amount,
            ':ref'    => $ref,
            ':notes'  => 'Transfer to ' . $recipient['email'],
            ':now'    => $now,
        ]);

        // 4. Recipient transaction record
        $db->prepare("
            INSERT INTO transactions
                   (user_id, type, amount, status, reference, notes, created_at)
            VALUES (:uid, 'transfer_received', :amount, 'completed', :ref, :notes, :now)
        ")->execute([
            ':uid'    => $recipient['id'],
            ':amount' => $amount,
            ':ref'    => $ref,
            ':notes'  => 'Transfer from ' . $sender['email'],
            ':now'    => $now,
        ]);

        // 5. Notification — sender
        $db->prepare("
            INSERT INTO notifications (user_id, title, message, type, created_at)
            VALUES (:uid, :title, :msg, 'transfer', :now)
        ")->execute([
            ':uid'   => $senderId,
            ':title' => 'Transfer Sent',
            ':msg'   => sprintf('You sent %s to %s.', formatMoney($amount), $recipient['email']),
            ':now'   => $now,
        ]);

        // 6. Notification — recipient
        $db->prepare("
            INSERT INTO notifications (user_id, title, message, type, created_at)
            VALUES (:uid, :title, :msg, 'transfer', :now)
        ")->execute([
            ':uid'   => $recipient['id'],
            ':title' => 'Funds Received',
            ':msg'   => sprintf('You received %s from %s.', formatMoney($amount), $sender['email']),
            ':now'   => $now,
        ]);

        $db->commit();

    } catch (\Throwable $txErr) {
        $db->rollBack();
        error_log('[transfer] Transaction rollback: ' . $txErr->getMessage());
        sendJsonResponse(false, 'Transfer failed due to a database error. Please try again.');
    }

    // ── Send email notifications (non-blocking — don't fail on SMTP error) ──
    try {
        emailTransferSent(
            $sender['email'],
            $sender['first_name'],
            formatMoney($amount),
            $recipient['email'],
            $ref
        );
        emailTransferReceived(
            $recipient['email'],
            $recipient['first_name'],
            formatMoney($amount),
            $sender['email'],
            $ref
        );
    } catch (\Throwable $mailErr) {
        error_log('[transfer] Email error: ' . $mailErr->getMessage());
    }

    sendJsonResponse(true, sprintf(
        'Successfully transferred %s to %s.',
        formatMoney($amount),
        $recipient['email']
    ));

} catch (\Throwable $e) {
    error_log('[transfer] ' . $e->getMessage());
    sendJsonResponse(false, 'Transfer failed. Please try again.');
}
