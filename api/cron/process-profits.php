<?php
/* =====================================================================
   api/cron/process-profits.php
   Daily profit processing engine.

   Run daily at midnight UTC:
     0 0 * * * php /path/to/api/cron/process-profits.php >> /var/log/averon-profits.log 2>&1

   Step 1 — Complete matured investments (end_date <= today):
     • Set status = completed
     • Return principal: balance += amount, invested_amount -= amount
     • Send investment-completed email
     • Insert notification

   Step 2 — Credit daily profit for remaining active investments:
     • Skips investments already credited today
     • dailyProfit = amount × daily_yield_rate / 100
     • profit_balance += dailyProfit
     • profit_earned  += dailyProfit
     • Insert profit transaction + notification
     • Send email only if dailyProfit > $1
   ===================================================================== */

// ── Access control: CLI only (or localhost for testing) ───────────────
if (php_sapi_name() !== 'cli') {
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($clientIp, ['127.0.0.1', '::1'], true)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once dirname(__DIR__) . '/utilities/helper.php';
require_once dirname(__DIR__) . '/utilities/email-templates.php';

$startTime = microtime(true);
$today     = date('Y-m-d');
echo '[process-profits] Starting at ' . date('Y-m-d H:i:s') . " UTC\n";

try {
    $db = Database::getInstance()->getConnection();

    // ==================================================================
    // STEP 1 — Complete matured investments
    // ==================================================================
    echo "[process-profits] Step 1: Checking for matured investments...\n";

    $stmtMatured = $db->prepare(
        'SELECT ui.id,
                ui.user_id,
                ui.plan_id,
                ui.amount,
                ui.profit_earned,
                ip.name AS plan_name,
                u.first_name,
                u.email
         FROM   user_investments ui
         JOIN   investment_plans ip ON ip.id = ui.plan_id
         JOIN   users            u  ON u.id  = ui.user_id
         WHERE  ui.status   = :status
           AND  ui.end_date <= CURDATE()'
    );
    $stmtMatured->execute([':status' => 'active']);
    $maturedInvestments = $stmtMatured->fetchAll();

    $completedCount = 0;

    foreach ($maturedInvestments as $inv) {
        $invId    = (int) $inv['id'];
        $uid      = (int) $inv['user_id'];
        $amount   = (float) $inv['amount'];
        $profit   = (float) $inv['profit_earned'];

        try {
            $db->beginTransaction();

            // Mark investment completed
            $db->prepare(
                'UPDATE user_investments
                 SET    status = :status
                 WHERE  id     = :id'
            )->execute([':status' => 'completed', ':id' => $invId]);

            // Return principal to balance, remove from invested_amount.
            // PDO with EMULATE_PREPARES=false disallows reusing the same
            // named param in one query — use distinct names for the two slots.
            $db->prepare(
                'UPDATE wallets
                 SET    balance         = balance         + :bal_add,
                        invested_amount = invested_amount - :inv_sub
                 WHERE  user_id = :uid'
            )->execute([':bal_add' => $amount, ':inv_sub' => $amount, ':uid' => $uid]);

            // Notification
            $db->prepare(
                'INSERT INTO notifications (user_id, title, message, type)
                 VALUES (:uid, :title, :message, :type)'
            )->execute([
                ':uid'     => $uid,
                ':title'   => 'Investment Completed',
                ':message' => 'Your ' . $inv['plan_name']
                              . ' investment of $' . number_format($amount, 2)
                              . ' has matured. Principal returned to your wallet.'
                              . ' Total profit earned: $' . number_format($profit, 2) . '.',
                ':type'    => 'investment',
            ]);

            $db->commit();

            // Email (non-blocking)
            emailInvestmentCompleted(
                $inv['email'],
                $inv['first_name'],
                $inv['plan_name'],
                number_format($amount, 2),
                number_format($profit, 2),
                $today
            );

            $completedCount++;
            echo "[process-profits] Completed investment #$invId (user $uid, \$$amount)\n";

        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('[process-profits] Failed to complete investment #' . $invId . ': ' . $e->getMessage());
            echo "[process-profits] ERROR completing investment #$invId: " . $e->getMessage() . "\n";
        }
    }

    echo "[process-profits] Step 1 done. Investments completed: $completedCount\n";

    // ==================================================================
    // STEP 2 — Credit daily profits for active investments
    // ==================================================================
    echo "[process-profits] Step 2: Crediting daily profits...\n";

    $stmtActive = $db->prepare(
        'SELECT ui.id,
                ui.user_id,
                ui.amount,
                ui.daily_yield_rate,
                ip.name AS plan_name,
                u.first_name,
                u.email
         FROM   user_investments ui
         JOIN   investment_plans ip ON ip.id = ui.plan_id
         JOIN   users            u  ON u.id  = ui.user_id
         WHERE  ui.status = :status
           AND  (ui.last_profit_credited IS NULL OR ui.last_profit_credited < CURDATE())'
    );
    $stmtActive->execute([':status' => 'active']);
    $activeInvestments = $stmtActive->fetchAll();

    $creditedCount = 0;

    foreach ($activeInvestments as $inv) {
        $invId      = (int) $inv['id'];
        $uid        = (int) $inv['user_id'];
        $amount     = (float) $inv['amount'];
        $dailyRate  = (float) $inv['daily_yield_rate']; // stored as e.g. 0.25 (= 0.25%)
        $dailyProfit = round($amount * $dailyRate / 100, 2);

        if ($dailyProfit <= 0) {
            continue;
        }

        try {
            $db->beginTransaction();

            // Credit profit_balance
            $db->prepare(
                'UPDATE wallets
                 SET    profit_balance = profit_balance + :profit
                 WHERE  user_id = :uid'
            )->execute([':profit' => $dailyProfit, ':uid' => $uid]);

            // Update investment record
            $db->prepare(
                'UPDATE user_investments
                 SET    profit_earned         = profit_earned + :profit,
                        last_profit_credited  = CURDATE()
                 WHERE  id = :id'
            )->execute([':profit' => $dailyProfit, ':id' => $invId]);

            // Insert profit transaction
            $profitRef = 'PROF-' . date('Ymd') . '-' . $invId;
            $db->prepare(
                'INSERT INTO transactions
                   (user_id, type, amount, currency, status, reference, notes, processed_at)
                 VALUES
                   (:uid, :type, :amount, :currency, :status, :ref, :notes, NOW())'
            )->execute([
                ':uid'      => $uid,
                ':type'     => 'profit',
                ':amount'   => $dailyProfit,
                ':currency' => 'USD',
                ':status'   => 'confirmed',
                ':ref'      => $profitRef,
                ':notes'    => 'Daily profit from ' . $inv['plan_name'] . ' (' . $dailyRate . '% rate)',
            ]);

            // Notification
            $db->prepare(
                'INSERT INTO notifications (user_id, title, message, type)
                 VALUES (:uid, :title, :message, :type)'
            )->execute([
                ':uid'     => $uid,
                ':title'   => 'Daily Profit Credited',
                ':message' => '$' . number_format($dailyProfit, 2)
                              . ' daily profit from your ' . $inv['plan_name']
                              . ' has been added to your profit balance.',
                ':type'    => 'profit',
            ]);

            $db->commit();

            // Email only if profit > $1 (avoid spam for tiny amounts)
            if ($dailyProfit > 1.00 && function_exists('emailProfitCredited')) {
                emailProfitCredited(
                    $inv['email'],
                    $inv['first_name'],
                    $inv['plan_name'],
                    number_format($dailyProfit, 2),
                    $today
                );
            }

            $creditedCount++;
            echo "[process-profits] Credited \$$dailyProfit to user $uid (investment #$invId)\n";

        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('[process-profits] Failed to credit profit for investment #' . $invId . ': ' . $e->getMessage());
            echo "[process-profits] ERROR crediting investment #$invId: " . $e->getMessage() . "\n";
        }
    }

    echo "[process-profits] Step 2 done. Profits credited: $creditedCount\n";

} catch (Throwable $e) {
    error_log('[process-profits] Fatal error: ' . $e->getMessage());
    echo '[process-profits] FATAL: ' . $e->getMessage() . "\n";
    exit(1);
}

$elapsed = round(microtime(true) - $startTime, 2);
echo "[process-profits] Finished in {$elapsed}s at " . date('Y-m-d H:i:s') . " UTC\n";
exit(0);
