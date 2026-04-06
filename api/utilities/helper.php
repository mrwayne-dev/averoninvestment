<?php
require_once dirname(__DIR__, 2) . '/config/constants.php';

// ============================================================
// Input / Output
// ============================================================

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Echo a JSON API response and exit.
 * @param bool   $success
 * @param string $message
 * @param array  $data
 */
function sendJsonResponse(bool $success, string $message, array $data = []): void {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}


// ============================================================
// Crypto helpers
// ============================================================

/**
 * Generate a cryptographically random numeric code.
 */
function generateCode(int $length = 6): string {
    $max  = (int) str_repeat('9', $length);
    $min  = (int) ('1' . str_repeat('0', $length - 1));
    return (string) random_int($min, $max);
}

/**
 * Generate a URL-safe random token.
 */
function generateToken(int $length = 32): string {
    // Each random_bytes byte produces 2 hex chars, so divide length by 2
    $bytes = (int) ceil($length / 2);
    return substr(bin2hex(random_bytes($bytes)), 0, $length);
}

/**
 * Derive a referral code from a user ID (base-36, uppercase, zero-padded to 8 chars).
 */
function generateReferralCode(int $userId): string {
    return str_pad(strtoupper(base_convert((string) $userId, 10, 36)), 8, '0', STR_PAD_LEFT);
}


// ============================================================
// Password
// ============================================================

function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword(string $password, string $hash): bool {
    return password_verify($password, $hash);
}


// ============================================================
// Validation
// ============================================================

function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Minimum 8 chars, at least 1 uppercase letter, 1 digit, 1 special character.
 */
function isStrongPassword(string $password): bool {
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password))            return false;
    if (!preg_match('/[0-9]/', $password))            return false;
    if (!preg_match('/[^A-Za-z0-9]/', $password))     return false;
    return true;
}


// ============================================================
// Formatting
// ============================================================

function formatMoney(float $amount, int $decimals = 2): string {
    return '$' . number_format($amount, $decimals);
}


// ============================================================
// Network
// ============================================================

function getRealIP(): string {
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            // X-Forwarded-For can be a comma-separated list; take the first IP
            $ip = trim(explode(',', $_SERVER[$header])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}


// ============================================================
// CSRF validation (for API endpoints only)
// ============================================================

/**
 * Validate the CSRF token sent by the client.
 *
 * Reads the token from:
 *   1. The X-CSRF-Token HTTP header  (AJAX requests)
 *   2. $_POST['_csrf_token']          (form fallback)
 *
 * Compares it against $_SESSION['csrf_token'] using a timing-safe comparison.
 * Calls sendJsonResponse(false, …) and exits if validation fails.
 */
function validateCsrfToken(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $sessionToken = $_SESSION['csrf_token'] ?? '';

    // Prefer the header (set by our apiRequest() helper in main.js)
    $clientToken  = $_SERVER['HTTP_X_CSRF_TOKEN']
                 ?? $_SERVER['HTTP_X_CSRF_Token']      // some servers normalise differently
                 ?? $_POST['_csrf_token']
                 ?? '';

    if (
        empty($sessionToken) ||
        empty($clientToken) ||
        !hash_equals($sessionToken, $clientToken)
    ) {
        sendJsonResponse(false, 'Invalid or missing security token. Please refresh and try again.');
    }
}


// ============================================================
// Auth guards (for API endpoints only — return JSON)
// ============================================================

function requireAuth(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        sendJsonResponse(false, 'Unauthorized', []);
    }
}

function requireAdmin(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        sendJsonResponse(false, 'Unauthorized', []);
    }
    if (($_SESSION['role'] ?? '') !== 'admin') {
        sendJsonResponse(false, 'Forbidden', []);
    }
}


// ============================================================
// Membership helpers
// ============================================================

/**
 * Return the active membership plan row for a user, or null if none.
 * Joins user_memberships → membership_plans.
 */
function getUserMembership(int $userId, PDO $db): ?array {
    $stmt = $db->prepare("
        SELECT mp.*
        FROM user_memberships um
        JOIN membership_plans mp ON mp.id = um.plan_id
        WHERE um.user_id = :uid
          AND um.status  = 'active'
          AND um.end_date >= CURDATE()
        ORDER BY um.created_at DESC
        LIMIT 1
    ");
    $stmt->execute(['uid' => $userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Return the max active investments allowed for a user.
 * Defaults to 2 (Basic tier) if no active membership.
 * NULL from the DB means unlimited (returns PHP_INT_MAX).
 */
function getMaxActiveInvestments(int $userId, PDO $db): int {
    $plan = getUserMembership($userId, $db);
    if ($plan === null) {
        return 2; // Basic default
    }
    if ($plan['max_active_investments'] === null) {
        return PHP_INT_MAX; // Unlimited (Platinum)
    }
    return (int) $plan['max_active_investments'];
}

/**
 * Return the referral commission rate (%) for a user.
 * Defaults to 3 (Basic tier) if no active membership.
 */
function getReferralCommissionRate(int $userId, PDO $db): float {
    $plan = getUserMembership($userId, $db);
    return $plan ? (float) $plan['referral_commission_pct'] : 3.0;
}
