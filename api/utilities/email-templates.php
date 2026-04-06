<?php
require_once dirname(__DIR__, 2) . '/config/email.php';

/**
 * Load an HTML email template and replace {{PLACEHOLDER}} tokens.
 */
function renderTemplate(string $name, array $vars = []): string {
    $path = dirname(__DIR__, 2) . '/assets/email-templates/' . $name;
    if (!file_exists($path)) {
        error_log('[EmailTemplates] Template not found: ' . $name);
        return '';
    }
    $html = file_get_contents($path);

    // Always inject these globals so every template footer works correctly
    $vars += [
        'app_url' => rtrim($_ENV['APP_URL'] ?? '', '/'),
        'year'    => date('Y'),
    ];

    foreach ($vars as $key => $value) {
        $html = str_replace('{{' . $key . '}}', (string) $value, $html);
    }
    return $html;
}

function emailVerify(string $to, string $firstName, string $code): bool {
    $html = renderTemplate('verify-email.html', [
        'verification_code' => $code,
    ]);
    return EmailService::send($to, 'Verify Your Email — Averon Investment', $html);
}

function emailWelcome(string $to, string $firstName, string $email, string $dashboardUrl): bool {
    $html = renderTemplate('welcome.html', [
        'first_name' => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
    ]);
    return EmailService::send($to, 'Welcome to Averon Investment', $html);
}

function emailPasswordReset(string $to, string $firstName, string $resetUrl): bool {
    $html = renderTemplate('password-reset.html', [
        'reset_url' => $resetUrl,
    ]);
    return EmailService::send($to, 'Reset Your Password — Averon Investment', $html);
}

function emailPasswordChanged(string $to, string $firstName, string $date, string $ip): bool {
    $html = renderTemplate('password-changed.html', [
        'FIRST_NAME' => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'DATE'       => $date,
        'IP_ADDRESS' => $ip,
    ]);
    return EmailService::send($to, 'Password Changed — Averon Investment', $html);
}

function emailWithdrawalPending(
    string $to,
    string $firstName,
    string $amount,
    string $currency,
    string $netAmount,
    string $feeAmount,
    string $reference,
    string $speedLabel
): bool {
    $html = renderTemplate('withdrawal-pending.html', [
        'first_name'       => htmlspecialchars($firstName,  ENT_QUOTES, 'UTF-8'),
        'amount'           => $amount,
        'processing_hours' => htmlspecialchars($speedLabel, ENT_QUOTES, 'UTF-8'),
    ]);
    return EmailService::send($to, 'Withdrawal Request Received — Averon Investment', $html);
}

function emailInvestmentStarted(
    string $to,
    string $firstName,
    string $planName,
    string $amount,
    string $startDate,
    string $endDate,
    string $profitDate,
    string $dailyRate
): bool {
    $html = renderTemplate('investment-started.html', [
        'first_name'      => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'plan_name'       => htmlspecialchars($planName,  ENT_QUOTES, 'UTF-8'),
        'amount'          => $amount,
        'daily_yield_min' => $dailyRate,
        'daily_yield_max' => $dailyRate,
        'duration_days'   => $endDate,
    ]);
    return EmailService::send($to, 'Investment Activated — Averon Investment', $html);
}

function emailMembershipEnrolled(
    string $to,
    string $firstName,
    string $planName,
    string $price,
    string $startDate,
    string $endDate,
    string $reference
): bool {
    $html = renderTemplate('membership-enrolled.html', [
        'first_name' => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'plan_name'  => htmlspecialchars($planName,  ENT_QUOTES, 'UTF-8'),
    ]);
    return EmailService::send($to, 'Membership Activated — Averon Investment', $html);
}

function emailDepositConfirmed(
    string $to,
    string $firstName,
    string $amount,
    string $currency,
    string $reference
): bool {
    $html = renderTemplate('deposit-confirmed.html', [
        'first_name' => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'amount'     => $amount,
        'currency'   => htmlspecialchars($currency,  ENT_QUOTES, 'UTF-8'),
        'payment_id' => htmlspecialchars($reference, ENT_QUOTES, 'UTF-8'),
    ]);
    return EmailService::send($to, 'Deposit Confirmed — Averon Investment', $html);
}

function emailInvestmentCompleted(
    string $to,
    string $firstName,
    string $planName,
    string $amount,
    string $totalProfit,
    string $endDate
): bool {
    $html = renderTemplate('investment-completed.html', [
        'first_name'      => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'plan_name'       => htmlspecialchars($planName,  ENT_QUOTES, 'UTF-8'),
        'amount'          => $amount,
        'total_profit'    => $totalProfit,
        'completion_date' => $endDate,
    ]);
    return EmailService::send($to, 'Investment Completed — Averon Investment', $html);
}

function emailWithdrawalConfirmed(
    string $to,
    string $firstName,
    string $amount,
    string $currency,
    string $reference
): bool {
    $html = renderTemplate('withdrawal-confirmed.html', [
        'first_name' => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'amount'     => $amount,
    ]);
    return EmailService::send($to, 'Withdrawal Processed — Averon Investment', $html);
}

/**
 * Notify user that their deposit is pending crypto confirmation.
 * Template: assets/email-templates/deposit-pending.html
 */
function emailDepositPending(
    string $to,
    string $firstName,
    string $amountUsd,
    string $currency,
    string $cryptoAmount,
    string $payAddress,
    string $reference
): bool {
    $html = renderTemplate('deposit-pending.html', [
        'first_name' => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
    ]);
    if (empty($html)) {
        return false; // Template not yet created — skip silently
    }
    return EmailService::send($to, 'Deposit Pending — Averon Investment', $html);
}

/**
 * Notify user that their membership has expired.
 * Template: assets/email-templates/membership-expired.html
 */
function emailMembershipExpired(
    string $to,
    string $firstName,
    string $planName,
    string $expiredDate
): bool {
    $html = renderTemplate('membership-expired.html', [
        'first_name' => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'plan_name'  => htmlspecialchars($planName,  ENT_QUOTES, 'UTF-8'),
    ]);
    if (empty($html)) {
        return false; // Template not yet created — skip silently
    }
    return EmailService::send($to, 'Membership Expired — Averon Investment', $html);
}

/**
 * Notify sender that their transfer was sent.
 * Template: assets/email-templates/transfer-sent.html
 */
function emailTransferSent(
    string $to,
    string $firstName,
    string $amount,
    string $recipientEmail,
    string $reference
): bool {
    $html = renderTemplate('transfer-sent.html', [
        'FIRST_NAME'      => htmlspecialchars($firstName,      ENT_QUOTES, 'UTF-8'),
        'AMOUNT'          => $amount,
        'RECIPIENT_EMAIL' => htmlspecialchars($recipientEmail, ENT_QUOTES, 'UTF-8'),
        'REFERENCE'       => htmlspecialchars($reference,      ENT_QUOTES, 'UTF-8'),
        'DASHBOARD_URL'   => rtrim($_ENV['APP_URL'] ?? '', '/') . '/dashboard/wallet',
    ]);
    if (empty($html)) return false;
    return EmailService::send($to, 'Transfer Sent — Averon Investment', $html);
}

/**
 * Notify recipient that they received a transfer.
 * Template: assets/email-templates/transfer-received.html
 */
function emailTransferReceived(
    string $to,
    string $firstName,
    string $amount,
    string $senderEmail,
    string $reference
): bool {
    $html = renderTemplate('transfer-received.html', [
        'FIRST_NAME'   => htmlspecialchars($firstName,   ENT_QUOTES, 'UTF-8'),
        'AMOUNT'       => $amount,
        'SENDER_EMAIL' => htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8'),
        'REFERENCE'    => htmlspecialchars($reference,   ENT_QUOTES, 'UTF-8'),
        'DASHBOARD_URL'=> rtrim($_ENV['APP_URL'] ?? '', '/') . '/dashboard/wallet',
    ]);
    if (empty($html)) return false;
    return EmailService::send($to, 'Funds Received — Averon Investment', $html);
}

/**
 * Notify admin of an account deletion request.
 */
function emailAccountDeletionAdmin(
    string $adminEmail,
    string $userFullName,
    string $userEmail,
    string $userId,
    string $requestDate
): bool {
    $appUrl  = rtrim($_ENV['APP_URL'] ?? '', '/');
    $subject = 'Account Deletion Request — Averon Investment';
    $body    = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:30px;">'
             . '<div style="max-width:600px;margin:0 auto;background:#fff;padding:32px;border-radius:8px;">'
             . '<h2 style="color:#BA2D0B;margin-top:0;">Account Deletion Request</h2>'
             . '<p>A user has submitted an account deletion request via the dashboard.</p>'
             . '<table style="width:100%;border-collapse:collapse;margin-top:16px;">'
             . '<tr><td style="padding:8px;font-weight:bold;width:140px;">User ID</td><td style="padding:8px;">' . htmlspecialchars($userId, ENT_QUOTES, 'UTF-8') . '</td></tr>'
             . '<tr style="background:#f9f9f9;"><td style="padding:8px;font-weight:bold;">Full Name</td><td style="padding:8px;">' . htmlspecialchars($userFullName, ENT_QUOTES, 'UTF-8') . '</td></tr>'
             . '<tr><td style="padding:8px;font-weight:bold;">Email</td><td style="padding:8px;">' . htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') . '</td></tr>'
             . '<tr style="background:#f9f9f9;"><td style="padding:8px;font-weight:bold;">Request Date</td><td style="padding:8px;">' . htmlspecialchars($requestDate, ENT_QUOTES, 'UTF-8') . '</td></tr>'
             . '</table>'
             . '<p style="margin-top:24px;">Please review the request in the <a href="' . $appUrl . '/admin/users" style="color:#BA2D0B;">Admin Panel</a> and process within 72 hours.</p>'
             . '</div></body></html>';
    return EmailService::send($adminEmail, $subject, $body);
}

/**
 * Confirm to the user that their deletion request was received.
 * Template: assets/email-templates/account-deletion-request.html
 */
function emailAccountDeletionUser(
    string $to,
    string $firstName
): bool {
    $html = renderTemplate('account-deletion-request.html', [
        'first_name'    => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'support_email' => htmlspecialchars($_ENV['SMTP_FROM'] ?? 'support@averon-investment.com', ENT_QUOTES, 'UTF-8'),
    ]);
    if (empty($html)) {
        // Fallback plain email if template not yet created
        $body = '<p>Hi ' . htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') . ',</p>'
              . '<p>We have received your account deletion request. Our team will review and process it within 72 hours.</p>'
              . '<p>If this was a mistake, please contact support immediately.</p>'
              . '<p>— Averon Investment Team</p>';
        return EmailService::send($to, 'Account Deletion Request Received — Averon Investment', $body);
    }
    return EmailService::send($to, 'Account Deletion Request Received — Averon Investment', $html);
}

/**
 * Notify user of their daily profit credit.
 * Only sent when profit > $1 to avoid inbox noise.
 * Template: assets/email-templates/profit-credited.html
 */
function emailProfitCredited(
    string $to,
    string $firstName,
    string $planName,
    string $profitAmount,
    string $date
): bool {
    $html = renderTemplate('profit-credited.html', [
        'first_name'    => htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
        'plan_name'     => htmlspecialchars($planName,  ENT_QUOTES, 'UTF-8'),
        'profit_amount' => $profitAmount,
        'credit_date'   => $date,
    ]);
    if (empty($html)) {
        return false; // Template not yet created — skip silently
    }
    return EmailService::send($to, 'Daily Profit Credited — Averon Investment', $html);
}
