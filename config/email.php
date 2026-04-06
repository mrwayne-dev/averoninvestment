<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

class EmailService {

    /**
     * Send an HTML email.
     *
     * @param string      $to        Recipient email address
     * @param string      $subject   Email subject
     * @param string      $htmlBody  Full HTML body
     * @param string|null $fromName  Override sender display name (optional)
     * @return bool  true on success, false on failure
     */
    public static function send(
        string $to,
        string $subject,
        string $htmlBody,
        ?string $fromName = null
    ): bool {
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? '';
            $mail->Port       = (int) ($_ENV['SMTP_PORT'] ?? 587);
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'] ?? '';
            $mail->Password   = $_ENV['SMTP_PASS'] ?? '';

            // Encryption: port 465 → SMTPS (SSL), all others → STARTTLS
            if ($mail->Port === 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Allow self-signed certs (common on cPanel/shared hosts like spacemail)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            // Sender
            $senderEmail = $_ENV['SMTP_FROM']      ?? 'noreply@averon-investment.com';
            $senderName  = $fromName
                ?? ($_ENV['SMTP_FROM_NAME'] ?? 'Averon Investment');

            $mail->setFrom($senderEmail, $senderName);
            $mail->addReplyTo($senderEmail, $senderName);

            // Recipient
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;

        } catch (MailerException $e) {
            error_log('[EmailService] Failed to send to ' . $to . ': ' . $mail->ErrorInfo);
            return false;
        } catch (\Throwable $e) {
            error_log('[EmailService] Unexpected error: ' . $e->getMessage());
            return false;
        }
    }
}
