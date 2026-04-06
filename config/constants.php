<?php
require_once __DIR__ . '/env.php';

// Paths
define('APP_ROOT',      dirname(__DIR__));
define('CONFIG_PATH',   APP_ROOT . '/config');
define('INCLUDES_PATH', APP_ROOT . '/includes');
define('UPLOADS_PATH',  APP_ROOT . '/uploads');

// App
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Averon Investment');
define('APP_URL',  rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'));
define('APP_ENV',  $_ENV['APP_ENV']  ?? 'production');

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

date_default_timezone_set('UTC');

// Currency
define('CURRENCY', 'USD');

// Session / auth
define('SESSION_TIMEOUT',     3600);
define('MAX_LOGIN_ATTEMPTS',  5);
define('CODE_EXPIRY',         900);   // 15 minutes for email verify codes

// Deposit / withdrawal limits
define('MIN_DEPOSIT',        50);
define('MAX_DEPOSIT',        500000);
define('MIN_WITHDRAWAL',     50);
define('WITHDRAWAL_FEE_PCT', 1.5);

// Admin registration — required invite code (empty string = registration disabled)
define('ADMIN_INVITE_CODE', $_ENV['ADMIN_INVITE_CODE'] ?? '');

// Support / Contact
define('SUPPORT_TELEGRAM_URL', $_ENV['SUPPORT_TELEGRAM_URL'] ?? 'https://t.me/AveronInvestmentSupport');

// NOWPayments
define('NOWPAYMENTS_API_URL',    'https://api.nowpayments.io/v1');
define('NOWPAYMENTS_API_KEY',    $_ENV['NOWPAYMENTS_API_KEY']    ?? '');
define('NOWPAYMENTS_IPN_SECRET', $_ENV['NOWPAYMENTS_IPN_SECRET'] ?? '');
define('SUPPORTED_CRYPTOS', ['BTC', 'ETH', 'USDTTRC20', 'USDTERC20']);
