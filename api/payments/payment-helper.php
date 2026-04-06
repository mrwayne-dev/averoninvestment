<?php
/* =====================================================================
   api/payments/payment-helper.php
   Shared deposit confirmation logic used by both:
     • check-payment.php  (user-triggered polling)
     • webhook.php        (NowPayments IPN)

   This function is IDEMPOTENT — calling it twice for the same
   transaction_id is safe; the second call will see the transaction
   is no longer 'pending' and will return false without any changes.
   ===================================================================== */

/**
 * Confirm a deposit: update transaction, credit wallet,
 * handle referral commission, notify, and send email.
 *
 * MUST be called outside any open transaction — this function
 * opens and commits its own transaction internally.
 *
 * @param PDO    $db            Active PDO connection
 * @param int    $transactionId transactions.id to confirm
 * @param int    $userId        Owner of the transaction
 * @param float  $amount        USD amount to credit
 * @param string $firstName     User's first name (for email)
 * @param string $userEmail     User's email address (for email)
 * @param string $currency      Display currency label (e.g. "BTC")
 * @param string $reference     NowPayments payment_id (for email / notes)
 *
 * @return bool  true if payment was confirmed, false if already confirmed
 * @throws Throwable on DB error (rolls back internally)
 */
function confirmDeposit(
    PDO    $db,
    int    $transactionId,
    int    $userId,
    float  $amount,
    string $firstName,
    string $userEmail,
    string $currency,
    string $reference
): bool {
    $db->beginTransaction();

    try {
        // ── 1. Lock transaction row; verify it is still pending ───────
        //    FOR UPDATE prevents double-processing under concurrent calls
        $stmtLock = $db->prepare(
            'SELECT id
             FROM   transactions
             WHERE  id      = :id
               AND  user_id = :uid
               AND  status  = :status
             FOR UPDATE'
        );
        $stmtLock->execute([
            ':id'     => $transactionId,
            ':uid'    => $userId,
            ':status' => 'pending',
        ]);

        if (!$stmtLock->fetch()) {
            // Idempotent: already confirmed (or different user — should not happen)
            $db->rollBack();
            return false;
        }

        // ── 2. Confirm the transaction ────────────────────────────────
        $db->prepare(
            'UPDATE transactions
             SET    status = :status, processed_at = NOW()
             WHERE  id = :id'
        )->execute([':status' => 'confirmed', ':id' => $transactionId]);

        // ── 3. Mark the NOWPayments order confirmed ───────────────────
        $db->prepare(
            'UPDATE nowpayments_orders
             SET    payment_status = :status
             WHERE  transaction_id = :tx_id'
        )->execute([':status' => 'confirmed', ':tx_id' => $transactionId]);

        // ── 4. Credit wallet balance (UPSERT — lazy provision) ────────
        // Note: PDO with EMULATE_PREPARES=false forbids reusing the same
        // named param in one query, so use distinct names for insert/update.
        $db->prepare(
            'INSERT INTO wallets (user_id, balance)
             VALUES (:uid, :bal_ins)
             ON DUPLICATE KEY UPDATE balance = balance + :bal_upd'
        )->execute([':uid' => $userId, ':bal_ins' => $amount, ':bal_upd' => $amount]);

        // ── 5. Insert deposit-confirmed notification ───────────────────
        $db->prepare(
            'INSERT INTO notifications (user_id, title, message, type)
             VALUES (:uid, :title, :message, :type)'
        )->execute([
            ':uid'     => $userId,
            ':title'   => 'Deposit Confirmed',
            ':message' => '$' . number_format($amount, 2)
                          . ' has been credited to your wallet. Reference: ' . $reference . '.',
            ':type'    => 'deposit',
        ]);

        // ── 6. Referral commission ────────────────────────────────────
        $stmtRef = $db->prepare(
            'SELECT id, referrer_id, commission_rate
             FROM   referrals
             WHERE  referred_id = :uid
             LIMIT  1'
        );
        $stmtRef->execute([':uid' => $userId]);
        $referral = $stmtRef->fetch();

        if ($referral) {
            $commissionRate = (float) $referral['commission_rate'];
            $commissionAmt  = round($amount * $commissionRate / 100, 2);
            $referrerId     = (int) $referral['referrer_id'];

            if ($commissionAmt > 0) {
                // Credit referrer's profit balance
                $db->prepare(
                    'INSERT INTO wallets (user_id, profit_balance)
                     VALUES (:uid, :pb_ins)
                     ON DUPLICATE KEY UPDATE profit_balance = profit_balance + :pb_upd'
                )->execute([':uid' => $referrerId, ':pb_ins' => $commissionAmt, ':pb_upd' => $commissionAmt]);

                // Update total_earned on referrals row
                $db->prepare(
                    'UPDATE referrals
                     SET    total_earned = total_earned + :amt
                     WHERE  id = :id'
                )->execute([':amt' => $commissionAmt, ':id' => $referral['id']]);

                // Record referral_bonus transaction for referrer
                $refTxRef = 'REF-' . strtoupper(bin2hex(random_bytes(4)));
                $db->prepare(
                    'INSERT INTO transactions
                       (user_id, type, amount, currency, status, reference, notes, processed_at)
                     VALUES
                       (:uid, :type, :amount, :currency, :status, :ref, :notes, NOW())'
                )->execute([
                    ':uid'      => $referrerId,
                    ':type'     => 'referral_bonus',
                    ':amount'   => $commissionAmt,
                    ':currency' => 'USD',
                    ':status'   => 'confirmed',
                    ':ref'      => $refTxRef,
                    ':notes'    => number_format($commissionRate, 2) . '% commission on deposit by user #' . $userId,
                ]);

                // Notify referrer
                $db->prepare(
                    'INSERT INTO notifications (user_id, title, message, type)
                     VALUES (:uid, :title, :message, :type)'
                )->execute([
                    ':uid'     => $referrerId,
                    ':title'   => 'Referral Commission Earned',
                    ':message' => 'You earned $' . number_format($commissionAmt, 2)
                                  . ' (' . number_format($commissionRate, 2) . '%) referral commission'
                                  . ' from a referred user\'s deposit.',
                    ':type'    => 'referral',
                ]);
            }
        }

        $db->commit();

        // ── 7. Send deposit-confirmed email (outside transaction) ─────
        emailDepositConfirmed(
            $userEmail,
            $firstName,
            number_format($amount, 2),
            $currency,
            $reference
        );

        return true;

    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
}
