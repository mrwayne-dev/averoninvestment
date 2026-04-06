<?php
/* =====================================================================
   api/utilities/rate-limiter.php
   Simple file-based IP rate limiter.

   Storage: sys_get_temp_dir() / rate_limit_{hash}.json
   Each file: { attempts: int, window_start: int }

   Usage:
     require_once '.../rate-limiter.php';

     if (!RateLimiter::checkLimit('login', getRealIP(), 5, 900)) {
         sendJsonResponse(false, 'Too many attempts. Please try again in 15 minutes.');
     }
     // ... attempt logic ...
     if ($failed) {
         RateLimiter::recordAttempt('login', getRealIP());
     } else {
         RateLimiter::clearAttempts('login', getRealIP());
     }
   ===================================================================== */

class RateLimiter
{
    /**
     * Returns true when the caller is WITHIN the allowed limit (i.e. can proceed).
     * Returns false when they have EXCEEDED the limit.
     *
     * @param string $action        Unique action key (e.g. 'login', 'register')
     * @param string $identifier    Client identifier, usually the IP address
     * @param int    $maxAttempts   Maximum allowed attempts within the window
     * @param int    $windowSeconds Length of the rolling window in seconds
     */
    public static function checkLimit(
        string $action,
        string $identifier,
        int    $maxAttempts   = 5,
        int    $windowSeconds = 900
    ): bool {
        $data = self::load($action, $identifier);

        // Reset if window has expired
        if ((time() - $data['window_start']) >= $windowSeconds) {
            return true; // Window passed — they're fine
        }

        return $data['attempts'] < $maxAttempts;
    }

    /**
     * Increment the attempt counter for a given action + identifier.
     * Resets the window if it has already expired.
     *
     * @param string $action
     * @param string $identifier
     * @param int    $windowSeconds
     */
    public static function recordAttempt(
        string $action,
        string $identifier,
        int    $windowSeconds = 900
    ): void {
        $data = self::load($action, $identifier);
        $now  = time();

        if (($now - $data['window_start']) >= $windowSeconds) {
            // Window expired — start a fresh one
            $data = ['attempts' => 0, 'window_start' => $now];
        }

        $data['attempts']++;
        self::save($action, $identifier, $data);
    }

    /**
     * Clear the rate-limit record (e.g. on successful login).
     *
     * @param string $action
     * @param string $identifier
     */
    public static function clearAttempts(string $action, string $identifier): void
    {
        $path = self::filePath($action, $identifier);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Returns how many seconds remain until the window resets,
     * or 0 if the window has already expired.
     *
     * @param string $action
     * @param string $identifier
     * @param int    $windowSeconds
     */
    public static function retryAfter(
        string $action,
        string $identifier,
        int    $windowSeconds = 900
    ): int {
        $data    = self::load($action, $identifier);
        $elapsed = time() - $data['window_start'];
        $remaining = $windowSeconds - $elapsed;
        return max(0, (int) $remaining);
    }

    // ── Private helpers ────────────────────────────────────────

    private static function filePath(string $action, string $identifier): string
    {
        $key = hash('sha256', $action . '|' . $identifier);
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_' . $key . '.json';
    }

    private static function load(string $action, string $identifier): array
    {
        $path = self::filePath($action, $identifier);
        if (!file_exists($path)) {
            return ['attempts' => 0, 'window_start' => time()];
        }
        $raw  = @file_get_contents($path);
        $data = $raw ? json_decode($raw, true) : null;
        if (!is_array($data) || !isset($data['attempts'], $data['window_start'])) {
            return ['attempts' => 0, 'window_start' => time()];
        }
        return $data;
    }

    private static function save(string $action, string $identifier, array $data): void
    {
        $path = self::filePath($action, $identifier);
        @file_put_contents($path, json_encode($data), LOCK_EX);
    }
}
