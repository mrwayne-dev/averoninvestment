<?php
/* =====================================================================
   ONE-TIME MIGRATION: Add 'banned' to users.status ENUM
   Run ONCE by visiting: /api/migrations/002-add-banned-status.php
   DELETE this file after running it.
   ===================================================================== */

header('Content-Type: text/plain');

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
        ALTER TABLE `users`
          MODIFY COLUMN `status`
            ENUM('pending','active','suspended','banned')
            NOT NULL DEFAULT 'pending'
    ");

    echo "Migration 002 applied successfully.\n";
    echo "users.status now accepts: pending, active, suspended, banned\n";
    echo "You can now delete this file.\n";

} catch (Exception $e) {
    http_response_code(500);
    echo "Migration failed: " . $e->getMessage() . "\n";
}
