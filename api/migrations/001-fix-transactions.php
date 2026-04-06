<?php
/* =====================================================================
   ONE-TIME MIGRATION: Fix transactions table ENUMs for transfer support
   Run ONCE by visiting: /api/migrations/001-fix-transactions.php
   DELETE this file after running it.
   ===================================================================== */

header('Content-Type: text/plain');

// Simple protection — only runs from localhost
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
    http_response_code(403);
    die("Forbidden: run this migration from localhost only.\n");
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

try {
    $db = Database::getInstance()->getConnection();

    $db->exec("
        ALTER TABLE `transactions`
          MODIFY COLUMN `type`   ENUM('deposit','withdrawal','profit','membership_fee',
                                      'referral_bonus','fee','transfer_sent','transfer_received') NOT NULL,
          MODIFY COLUMN `status` ENUM('pending','confirmed','failed','cancelled','completed')
                                      NOT NULL DEFAULT 'pending'
    ");

    echo "Migration 001 applied successfully.\n";
    echo "You can now delete this file.\n";

} catch (Exception $e) {
    http_response_code(500);
    echo "Migration failed: " . $e->getMessage() . "\n";
}
