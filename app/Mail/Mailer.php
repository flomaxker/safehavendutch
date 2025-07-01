<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = MAIL_HOST; // Defined in .env
        $this->mail->SMTPAuth = true;
        $this->mail->Username = MAIL_USERNAME; // Defined in .env
        $this->mail->Password = MAIL_PASSWORD; // Defined in .env
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = MAIL_PORT;

        // Recipients
        $this->mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $this->mail->isHTML(true);
    }

    public function send(string $toEmail, string $toName, string $subject, string $body, string $altBody = ''): bool
    {
        try {
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function loadTemplate(string $templatePath, array $data = []): string
    {
        ob_start();
        extract($data);
        include $templatePath;
        return ob_get_clean();
    }
}
