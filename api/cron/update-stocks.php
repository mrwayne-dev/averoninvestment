<?php
/* =====================================================================
   api/cron/update-stocks.php
   Fetches the latest TSLA stock data from Yahoo Finance and updates
   the tesla_stocks table.

   Run every 15 minutes:
     // */15 * * * * php /path/to/api/cron/update-stocks.php >> /var/log/averon-stocks.log 2>&1

   Source: Yahoo Finance Chart API (public, no auth required)
   URL: https://query1.finance.yahoo.com/v8/finance/chart/TSLA?interval=1d&range=1d
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

echo '[update-stocks] Starting at ' . date('Y-m-d H:i:s') . " UTC\n";

// ── Fetch data from Yahoo Finance ─────────────────────────────────────
$url = 'https://query1.finance.yahoo.com/v8/finance/chart/TSLA?interval=1d&range=1d';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; AveronInvestmentBot/1.0)',
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
    ],
]);

$response  = curl_exec($ch);
$httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError !== '') {
    error_log('[update-stocks] cURL error: ' . $curlError);
    echo "[update-stocks] cURL error: $curlError\n";
    exit(1);
}

if ($httpCode !== 200 || $response === false || $response === '') {
    error_log('[update-stocks] Bad response. HTTP ' . $httpCode);
    echo "[update-stocks] Bad HTTP response: $httpCode\n";
    exit(1);
}

// ── Parse response ────────────────────────────────────────────────────
$data = json_decode($response, true);

if (!is_array($data) || empty($data['chart']['result'][0])) {
    error_log('[update-stocks] Could not parse Yahoo Finance response.');
    echo "[update-stocks] Failed to parse response.\n";
    exit(1);
}

$meta = $data['chart']['result'][0]['meta'] ?? [];

$price         = (float) ($meta['regularMarketPrice']  ?? 0);
$previousClose = (float) ($meta['chartPreviousClose']  ?? $meta['previousClose'] ?? $price);
$volume        = (int)   ($meta['regularMarketVolume'] ?? 0);

if ($price <= 0) {
    echo "[update-stocks] Invalid price returned ($price). Aborting.\n";
    exit(1);
}

$changeAmount  = round($price - $previousClose, 2);
$changePercent = $previousClose > 0
    ? round(($changeAmount / $previousClose) * 100, 2)
    : 0.0;

echo "[update-stocks] TSLA price=\${$price} change={$changeAmount} ({$changePercent}%) volume={$volume}\n";

// ── Upsert tesla_stocks ───────────────────────────────────────────────
try {
    $db = Database::getInstance()->getConnection();

    // Check for existing TSLA row
    $stmtCheck = $db->prepare(
        'SELECT id FROM tesla_stocks WHERE symbol = :symbol ORDER BY id DESC LIMIT 1'
    );
    $stmtCheck->execute([':symbol' => 'TSLA']);
    $existingId = $stmtCheck->fetchColumn();

    if ($existingId) {
        // Update the existing row
        $db->prepare(
            'UPDATE tesla_stocks
             SET    price          = :price,
                    change_amount  = :change_amount,
                    change_percent = :change_percent,
                    volume         = :volume,
                    updated_at     = NOW()
             WHERE  id = :id'
        )->execute([
            ':price'          => $price,
            ':change_amount'  => $changeAmount,
            ':change_percent' => $changePercent,
            ':volume'         => $volume,
            ':id'             => $existingId,
        ]);
        echo "[update-stocks] Updated existing row (id=$existingId).\n";
    } else {
        // Insert first row
        $db->prepare(
            'INSERT INTO tesla_stocks
               (symbol, price, change_amount, change_percent, volume)
             VALUES
               (:symbol, :price, :change_amount, :change_percent, :volume)'
        )->execute([
            ':symbol'         => 'TSLA',
            ':price'          => $price,
            ':change_amount'  => $changeAmount,
            ':change_percent' => $changePercent,
            ':volume'         => $volume,
        ]);
        echo "[update-stocks] Inserted new row.\n";
    }

} catch (Throwable $e) {
    error_log('[update-stocks] DB error: ' . $e->getMessage());
    echo '[update-stocks] DB error: ' . $e->getMessage() . "\n";
    exit(1);
}

echo '[update-stocks] Done at ' . date('Y-m-d H:i:s') . " UTC\n";
exit(0);
