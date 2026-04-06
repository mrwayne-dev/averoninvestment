<?php
/* =====================================================================
   api/admin-dashboard/update-user.php
   Admin endpoint to update user fields: balance, profit_balance,
   status (active/suspended/banned), role, and basic profile.

   Method: POST (JSON body)
   Auth:   Admin session required

   Body:
     user_id         — int (required)
     action          — 'update_balance' | 'update_status' | 'update_profile' | 'delete'
     balance         — float (for update_balance)
     profit_balance  — float (for update_balance)
     invested_amount — float (for update_balance)
     status          — 'active'|'suspended'|'banned' (for update_status)
     first_name      — string (for update_profile)
     last_name       — string (for update_profile)
     email           — string (for update_profile)
   ===================================================================== */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once '../../config/database.php';
require_once '../../config/constants.php';

// ── Auth ──────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$userId = isset($input['user_id']) ? (int) $input['user_id'] : 0;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'user_id is required']);
    exit;
}

// Prevent admin from modifying their own account via this endpoint
if ($userId === (int) $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Cannot modify your own account via this endpoint']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // Verify user exists
    $stmtCheck = $db->prepare('SELECT id, role FROM users WHERE id = :id LIMIT 1');
    $stmtCheck->execute([':id' => $userId]);
    $targetUser = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$targetUser) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // ── Actions ───────────────────────────────────────────────────
    switch ($action) {

        // ── Update wallet balances ────────────────────────────────
        case 'update_balance':
            $balance        = isset($input['balance'])        ? max(0, (float) $input['balance'])        : null;
            $profitBalance  = isset($input['profit_balance']) ? max(0, (float) $input['profit_balance']) : null;
            $investedAmount = isset($input['invested_amount'])? max(0, (float) $input['invested_amount']): null;

            // Build dynamic SET clause
            $sets   = [];
            $params = [':uid' => $userId];

            if ($balance !== null)        { $sets[] = 'balance = :balance';               $params[':balance']        = $balance; }
            if ($profitBalance !== null)  { $sets[] = 'profit_balance = :profit_balance'; $params[':profit_balance'] = $profitBalance; }
            if ($investedAmount !== null) { $sets[] = 'invested_amount = :invested_amount'; $params[':invested_amount'] = $investedAmount; }

            if (empty($sets)) {
                echo json_encode(['success' => false, 'message' => 'No balance fields provided']);
                exit;
            }

            $db->prepare(
                'INSERT INTO wallets (user_id, balance, profit_balance, invested_amount)
                 VALUES (:uid, :balance_ins, :profit_ins, :invested_ins)
                 ON DUPLICATE KEY UPDATE ' . implode(', ', $sets)
            )->execute(array_merge($params, [
                ':balance_ins'  => $balance        ?? 0,
                ':profit_ins'   => $profitBalance  ?? 0,
                ':invested_ins' => $investedAmount ?? 0,
            ]));

            echo json_encode(['success' => true, 'message' => 'Balance updated successfully']);
            break;

        // ── Update account status ─────────────────────────────────
        case 'update_status':
            $allowed = ['active', 'suspended', 'banned'];
            $status  = $input['status'] ?? '';

            if (!in_array($status, $allowed, true)) {
                echo json_encode(['success' => false, 'message' => 'Invalid status value']);
                exit;
            }

            // Prevent de-activating other admins
            if ($targetUser['role'] === 'admin' && $status !== 'active') {
                echo json_encode(['success' => false, 'message' => 'Cannot suspend or ban admin accounts']);
                exit;
            }

            $db->prepare('UPDATE users SET status = :status WHERE id = :id')
               ->execute([':status' => $status, ':id' => $userId]);

            echo json_encode(['success' => true, 'message' => 'User status updated to ' . $status]);
            break;

        // ── Update profile info ───────────────────────────────────
        case 'update_profile':
            $firstName = trim($input['first_name'] ?? '');
            $lastName  = trim($input['last_name']  ?? '');
            $email     = trim($input['email']      ?? '');

            if ($firstName === '' || $email === '') {
                echo json_encode(['success' => false, 'message' => 'first_name and email are required']);
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                exit;
            }

            // Check email uniqueness (excluding this user)
            $stmtEmail = $db->prepare('SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1');
            $stmtEmail->execute([':email' => $email, ':id' => $userId]);
            if ($stmtEmail->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Email already in use by another account']);
                exit;
            }

            $db->prepare(
                'UPDATE users SET first_name = :fn, last_name = :ln, email = :email WHERE id = :id'
            )->execute([':fn' => $firstName, ':ln' => $lastName, ':email' => $email, ':id' => $userId]);

            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            break;

        // ── Soft-delete / anonymise account ───────────────────────
        case 'delete':
            if ($targetUser['role'] === 'admin') {
                echo json_encode(['success' => false, 'message' => 'Cannot delete admin accounts']);
                exit;
            }

            $db->beginTransaction();
            try {
                // Anonymise PII
                $db->prepare(
                    'UPDATE users SET
                       email      = CONCAT(\'deleted_\', id, \'@removed.invalid\'),
                       first_name = \'Deleted\',
                       last_name  = \'User\',
                       status     = \'banned\',
                       password   = \'\'
                     WHERE id = :id'
                )->execute([':id' => $userId]);

                // Record notification
                $db->prepare(
                    'INSERT INTO notifications (user_id, title, message, type)
                     VALUES (:uid, :t, :m, :type)'
                )->execute([
                    ':uid'  => $userId,
                    ':t'    => 'Account Closed',
                    ':m'    => 'Your account has been closed by an administrator.',
                    ':type' => 'warning',
                ]);

                $db->commit();
                echo json_encode(['success' => true, 'message' => 'User account has been deleted']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
