<?php
/**
 * PortfolioForge Mail Configuration
 * Wrapper for PHPMailer to send emails via Gmail SMTP.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

/**
 * Send an email using PHPMailer SMTP.
 *
 * @param string $to Recipient email address
 * @param string $subject Subject of the email
 * @param string $body HTML body of the email
 * @param string $altBody Plain text body of the email (optional)
 * @return bool True on success, false on failure
 */
function sendMail($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com';      // Replace with your Gmail address
        $mail->Password   = 'your_app_password';        // Replace with your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('your_email@gmail.com', 'PortfolioForge');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error (for debugging)
        error_log("Mail send failed: {$mail->ErrorInfo}");
        return false;
    }
}
