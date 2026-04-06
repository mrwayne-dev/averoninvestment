<?php
/* =====================================================================
   api/admin-dashboard/manage-transaction.php
   Admin endpoint for transaction management.

   Method: POST (JSON body)
   Auth:   Admin session required

   Actions:
     approve_deposit    — Manually confirm a pending deposit + credit wallet
     reject_deposit     — Mark pending deposit as failed
     approve_withdrawal — Mark pending withdrawal as completed + notify
     reject_withdrawal  — Reject a pending withdrawal + refund to wallet
     get_list           — Paginated list of transactions with filters
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

$method = $_SERVER['REQUEST_METHOD'];

// GET for listing
if ($method === 'GET') {
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    try {
        $db = Database::getInstance()->getConnection();

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $limit   = 20;
        $offset  = ($page - 1) * $limit;
        $type    = $_GET['type']   ?? '';
        $status  = $_GET['status'] ?? '';
        $search  = trim($_GET['search'] ?? '');
        $dateFrom = $_GET['from'] ?? '';
        $dateTo   = $_GET['to']   ?? '';

        $where  = [];
        $params = [];

        if ($type)   { $where[] = 't.type = :type';   $params[':type']   = $type; }
        if ($status) { $where[] = 't.status = :status'; $params[':status'] = $status; }
        if ($dateFrom) { $where[] = 'DATE(t.created_at) >= :from'; $params[':from'] = $dateFrom; }
        if ($dateTo)   { $where[] = 'DATE(t.created_at) <= :to';   $params[':to']   = $dateTo; }
        if ($search) {
            $where[] = '(u.email LIKE :search OR u.first_name LIKE :search2 OR t.reference LIKE :search3)';
            $params[':search']  = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count
        $stmtCount = $db->prepare(
            'SELECT COUNT(*) FROM transactions t
             JOIN users u ON u.id = t.user_id ' . $whereClause
        );
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        // Data
        $stmtData = $db->prepare(
            'SELECT t.id, t.type, t.amount, t.status, t.reference, t.created_at, t.updated_at,
                    u.id AS user_id, u.first_name, u.last_name, u.email
             FROM transactions t
             JOIN users u ON u.id = t.user_id
             ' . $whereClause . '
             ORDER BY t.created_at DESC
             LIMIT ' . $limit . ' OFFSET ' . $offset
        );
        $stmtData->execute($params);
        $transactions = $stmtData->fetchAll(PDO::FETCH_ASSOC);
        foreach ($transactions as &$tx) {
            $tx['amount'] = (float) $tx['amount'];
            $tx['name']   = trim($tx['first_name'] . ' ' . $tx['last_name']);
        }
        unset($tx);

        echo json_encode([
            'success' => true,
            'data'    => [
                'transactions' => $transactions,
                'total'        => $total,
                'page'         => $page,
                'pages'        => (int) ceil($total / $limit),
                'limit'        => $limit,
            ],
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
    exit;
}

// POST for actions
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action']         ?? '';
$txId   = isset($input['transaction_id']) ? (int) $input['transaction_id'] : 0;

if (!$txId && !in_array($action, ['get_list', 'export_csv'], true)) {
    echo json_encode(['success' => false, 'message' => 'transaction_id is required']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    // Load transaction
    if ($txId) {
        $stmtTx = $db->prepare(
            'SELECT t.*, u.first_name, u.last_name, u.email
             FROM transactions t
             JOIN users u ON u.id = t.user_id
             WHERE t.id = :id LIMIT 1'
        );
        $stmtTx->execute([':id' => $txId]);
        $tx = $stmtTx->fetch(PDO::FETCH_ASSOC);

        if (!$tx) {
            echo json_encode(['success' => false, 'message' => 'Transaction not found']);
            exit;
        }
    }

    switch ($action) {

        // ── Approve a pending deposit manually ────────────────────
        case 'approve_deposit':
            if ($tx['type'] !== 'deposit') {
                echo json_encode(['success' => false, 'message' => 'Transaction is not a deposit']);
                exit;
            }
            if ($tx['status'] !== 'pending') {
                echo json_encode(['success' => false, 'message' => 'Transaction is not in pending status']);
                exit;
            }

            $db->beginTransaction();
            try {
                $amount = (float) $tx['amount'];
                $uid    = (int)   $tx['user_id'];

                // Confirm transaction
                $db->prepare('UPDATE transactions SET status = \'confirmed\', updated_at = NOW() WHERE id = :id')
                   ->execute([':id' => $txId]);

                // Credit wallet
                $db->prepare(
                    'INSERT INTO wallets (user_id, balance) VALUES (:uid, :amt)
                     ON DUPLICATE KEY UPDATE balance = balance + :amt2'
                )->execute([':uid' => $uid, ':amt' => $amount, ':amt2' => $amount]);

                // Notification
                $db->prepare(
                    'INSERT INTO notifications (user_id, title, message, type)
                     VALUES (:uid, :t, :m, :type)'
                )->execute([
                    ':uid'  => $uid,
                    ':t'    => 'Deposit Confirmed',
                    ':m'    => 'Your deposit of $' . number_format($amount, 2) . ' has been confirmed.',
                    ':type' => 'success',
                ]);

                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Deposit approved and wallet credited']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        // ── Reject a pending deposit ───────────────────────────────
        case 'reject_deposit':
            if ($tx['type'] !== 'deposit') {
                echo json_encode(['success' => false, 'message' => 'Transaction is not a deposit']);
                exit;
            }
            if ($tx['status'] !== 'pending') {
                echo json_encode(['success' => false, 'message' => 'Transaction is not pending']);
                exit;
            }

            $db->prepare('UPDATE transactions SET status = \'failed\', updated_at = NOW() WHERE id = :id')
               ->execute([':id' => $txId]);

            // Notify user
            $db->prepare(
                'INSERT INTO notifications (user_id, title, message, type)
                 VALUES (:uid, :t, :m, :type)'
            )->execute([
                ':uid'  => (int) $tx['user_id'],
                ':t'    => 'Deposit Failed',
                ':m'    => 'Your deposit of $' . number_format((float)$tx['amount'], 2) . ' could not be confirmed.',
                ':type' => 'error',
            ]);

            echo json_encode(['success' => true, 'message' => 'Deposit rejected']);
            break;

        // ── Approve a pending withdrawal ───────────────────────────
        case 'approve_withdrawal':
            if ($tx['type'] !== 'withdrawal') {
                echo json_encode(['success' => false, 'message' => 'Transaction is not a withdrawal']);
                exit;
            }
            if (!in_array($tx['status'], ['pending', 'processing'], true)) {
                echo json_encode(['success' => false, 'message' => 'Transaction is not in a pending/processing state']);
                exit;
            }

            $db->prepare('UPDATE transactions SET status = \'completed\', updated_at = NOW() WHERE id = :id')
               ->execute([':id' => $txId]);

            $db->prepare(
                'INSERT INTO notifications (user_id, title, message, type)
                 VALUES (:uid, :t, :m, :type)'
            )->execute([
                ':uid'  => (int) $tx['user_id'],
                ':t'    => 'Withdrawal Completed',
                ':m'    => 'Your withdrawal of $' . number_format((float)$tx['amount'], 2) . ' has been processed.',
                ':type' => 'success',
            ]);

            echo json_encode(['success' => true, 'message' => 'Withdrawal marked as completed']);
            break;

        // ── Reject a withdrawal + refund ──────────────────────────
        case 'reject_withdrawal':
            if ($tx['type'] !== 'withdrawal') {
                echo json_encode(['success' => false, 'message' => 'Transaction is not a withdrawal']);
                exit;
            }
            if (!in_array($tx['status'], ['pending', 'processing'], true)) {
                echo json_encode(['success' => false, 'message' => 'Transaction is not in a pending/processing state']);
                exit;
            }

            $db->beginTransaction();
            try {
                $amount = (float) $tx['amount'];
                $uid    = (int)   $tx['user_id'];

                // Fail the withdrawal
                $db->prepare('UPDATE transactions SET status = \'failed\', updated_at = NOW() WHERE id = :id')
                   ->execute([':id' => $txId]);

                // Refund to wallet balance
                $db->prepare(
                    'INSERT INTO wallets (user_id, balance) VALUES (:uid, :amt)
                     ON DUPLICATE KEY UPDATE balance = balance + :amt2'
                )->execute([':uid' => $uid, ':amt' => $amount, ':amt2' => $amount]);

                // Notification
                $db->prepare(
                    'INSERT INTO notifications (user_id, title, message, type)
                     VALUES (:uid, :t, :m, :type)'
                )->execute([
                    ':uid'  => $uid,
                    ':t'    => 'Withdrawal Rejected',
                    ':m'    => 'Your withdrawal of $' . number_format($amount, 2) . ' was rejected. Funds have been returned to your wallet.',
                    ':type' => 'warning',
                ]);

                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Withdrawal rejected and funds refunded']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
