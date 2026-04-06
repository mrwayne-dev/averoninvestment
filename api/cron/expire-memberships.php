<?php
/* =====================================================================
   api/cron/expire-memberships.php
   Marks active memberships as expired when their end_date has passed.

   Run daily at 1am UTC:
     0 1 * * * php /path/to/api/cron/expire-memberships.php >> /var/log/averon-memberships.log 2>&1
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
echo '[expire-memberships] Starting at ' . date('Y-m-d H:i:s') . " UTC\n";

try {
    $db = Database::getInstance()->getConnection();

    // ── Fetch all memberships that have expired ───────────────────────
    $stmtExpired = $db->prepare(
        'SELECT um.id,
                um.user_id,
                um.plan_id,
                mp.name AS plan_name,
                u.first_name,
                u.email,
                um.end_date
         FROM   user_memberships um
         JOIN   membership_plans mp ON mp.id = um.plan_id
         JOIN   users            u  ON u.id  = um.user_id
         WHERE  um.status   = :status
           AND  um.end_date <= CURDATE()'
    );
    $stmtExpired->execute([':status' => 'active']);
    $expiredMemberships = $stmtExpired->fetchAll();

    $expiredCount = 0;

    foreach ($expiredMemberships as $membership) {
        $membershipId = (int) $membership['id'];
        $uid          = (int) $membership['user_id'];

        try {
            // ── Mark membership expired ───────────────────────────────
            $db->prepare(
                'UPDATE user_memberships
                 SET    status = :status
                 WHERE  id     = :id'
            )->execute([':status' => 'expired', ':id' => $membershipId]);

            // ── Insert notification ────────────────────────────────────
            $db->prepare(
                'INSERT INTO notifications (user_id, title, message, type)
                 VALUES (:uid, :title, :message, :type)'
            )->execute([
                ':uid'     => $uid,
                ':title'   => 'Membership Expired',
                ':message' => 'Your ' . $membership['plan_name']
                              . ' membership expired on ' . $membership['end_date']
                              . '. Renew to keep your investment limits and withdrawal speeds.',
                ':type'    => 'membership',
            ]);

            // ── Send membership-expired email (non-blocking) ──────────
            if (function_exists('emailMembershipExpired')) {
                emailMembershipExpired(
                    $membership['email'],
                    $membership['first_name'],
                    $membership['plan_name'],
                    $membership['end_date']
                );
            }

            $expiredCount++;
            echo "[expire-memberships] Expired membership #$membershipId for user $uid ({$membership['plan_name']})\n";

        } catch (Throwable $e) {
            error_log('[expire-memberships] Failed for membership #' . $membershipId . ': ' . $e->getMessage());
            echo "[expire-memberships] ERROR for membership #$membershipId: " . $e->getMessage() . "\n";
        }
    }

    echo "[expire-memberships] Done. Expired: $expiredCount memberships.\n";

} catch (Throwable $e) {
    error_log('[expire-memberships] Fatal error: ' . $e->getMessage());
    echo '[expire-memberships] FATAL: ' . $e->getMessage() . "\n";
    exit(1);
}

$elapsed = round(microtime(true) - $startTime, 2);
echo "[expire-memberships] Finished in {$elapsed}s at " . date('Y-m-d H:i:s') . " UTC\n";
exit(0);
