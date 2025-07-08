<?php
require_once __DIR__ . '/bootstrap.php';

use App\Models\Setting;

// --- Configuration ---
// These values should ideally come from a CMS configuration or database.
$settingModel = new Setting($container->getPdo());
$settings = $settingModel->getAllSettings();

$recipient_email = $settings['contact_recipient_email'] ?? "info@yourcms.com";
$server_mandated_from_email = $settings['contact_from_email'] ?? "noreply@yourcms.com";
$from_display_name = $settings['contact_from_name'] ?? "Your CMS Contact Form";
$email_subject_prefix = "New Contact Form Submission";

// --- Initialize Response Array ---
$response = ['success' => false, 'errors' => []];

// --- Check Request Method ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Get and Trim Input ---
    $user_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $user_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $user_subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $user_message = isset($_POST['message']) ? trim($_POST['message']) : ''; // Raw message

    // --- Server-side Validate Input ---
    if (empty($user_name)) {
        $response['errors'][] = "Name is required.";
    }
    if (empty($user_email)) {
        $response['errors'][] = "Email is required.";
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Invalid email format.";
    }
    if (empty($user_message)) {
        $response['errors'][] = "Message is required.";
    }
    if (empty($user_subject)) {
        $response['errors'][] = "Subject is required.";
    }

    if (!empty($response['errors'])) {
        http_response_code(400);
    } else {
        // --- Sanitize inputs appropriately ---
        $display_user_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
        $display_user_email = htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8');
        $display_user_subject = htmlspecialchars($user_subject, ENT_QUOTES, 'UTF-8');
        $message_for_html_body = nl2br(htmlspecialchars($user_message, ENT_QUOTES, 'UTF-8'));

        // --- Construct Email Content ---
        $email_subject = $email_subject_prefix . ": " . $display_user_subject;

        // Construct HTML Email Body
        $email_body = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'>";
        $email_body .= "<title>" . htmlspecialchars($email_subject, ENT_QUOTES, 'UTF-8') . "</title>";
        $email_body .= "<style>";
        $email_body .= "body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }";
        $email_body .= ".container { width: 90%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }";
        $email_body .= "h2 { color: #2c3e50; margin-top:0; }";
        $email_body .= "p { margin-bottom: 10px; }";
        $email_body .= "strong { color: #34495e; }";
        $email_body .= ".message-content { padding: 15px; background-color: #ffffff; border: 1px solid #eee; border-radius: 4px; margin-top: 5px; white-space: pre-wrap; word-wrap: break-word; }";
        $email_body .= "hr { border: 0; height: 1px; background: #ddd; margin: 20px 0; }";
        $email_body .= ".footer { font-size: 0.9em; color: #7f8c8d; text-align: center; margin-top: 20px;}";
        $email_body .= "</style></head><body>";
        $email_body .= "<div class='container'>";
        $email_body .= "<h2>New Contact Form Submission</h2>";
        $email_body .= "<p>You have received a new message from your website contact form:</p>";
        $email_body .= "<hr>";
        $email_body .= "<p><strong>Name:</strong> " . $display_user_name . "</p>";
        $email_body .= "<p><strong>Email:</strong> <a href='mailto:" . rawurlencode($user_email) . "'>" . $display_user_email . "</a></p>";
        $email_body .= "<p><strong>Message:</strong></p>";
        $email_body .= "<div class='message-content'>" . $message_for_html_body . "</div>";
        $email_body .= "<hr>";
        $email_body .= "<p class='footer'><em>Sent via Website Contact Form</em></p>";
        $email_body .= "</div>";
        $email_body .= "</body></html>";

        // --- PHPMailer Setup ---
        require_once __DIR__ . '/vendor/autoload.php'; // Adjust path if necessary
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true); // Passing true enables exceptions

        try {
            //Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = getenv('SMTP_HOST');                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = getenv('SMTP_USERNAME');                 // SMTP username
            $mail->Password   = getenv('SMTP_PASSWORD');                 // SMTP password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = getenv('SMTP_PORT');                     // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($server_mandated_from_email, $from_display_name);
            $mail->addAddress($recipient_email);                        // Add a recipient
            $mail->addReplyTo($user_email, $user_name);                 // Reply to the user's email

            //Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = $email_subject;
            $mail->Body    = $email_body;
            $mail->AltBody = strip_tags($user_message); // Plain text for non-HTML mail clients

            $mail->send();
            $response['success'] = true;
            http_response_code(200);
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}", 0);
            $response['errors'][] = "Message could not be sent due to a server issue. Please try again.";
            $response['detailed_error_guess'] = "Mailer Error: {$mail->ErrorInfo}";
            http_response_code(500);
        }
    }
} else {
    $response['errors'][] = "Invalid request method.";
    http_response_code(405);
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>