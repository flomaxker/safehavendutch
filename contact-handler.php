<?php
require_once __DIR__ . '/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// --- Configuration ---
$recipient_email = "hello@safehavendutch.nl";
$server_mandated_from_email = "noreply@kersten.online";
$from_display_name = "Safe Haven Dutch Coaching Contact Form";
$email_subject_prefix = "New Contact Form Submission";

// --- Initialize Response Array ---
$response = ['success' => false, 'errors' => []];

// --- Check Request Method ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Optional: Keep logging for a bit if you like
    // error_log("HTML_EMAIL_MAILER - POST DATA RECEIVED: " . print_r($_POST, true), 0);

    // --- Get and Trim Input ---
    $user_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    // Remove CRLF characters to prevent header injection
    $user_name = str_replace(["\r", "\n"], '', $user_name);
    $user_email = isset($_POST['email']) ? trim($_POST['email']) : '';
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

    if (!empty($response['errors'])) {
        http_response_code(400);
        // error_log("HTML_EMAIL_MAILER - Validation errors: " . print_r($response['errors'], true), 0);
    } else {
        // --- Sanitize inputs appropriately ---
        // For display in HTML (subject, body names, etc.), htmlspecialchars is good.
        $display_user_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
        $display_user_email = htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); // For display in HTML body

        // Prepare values for the Reply-To header. The email has already been validated.
        // Use the sanitized name to avoid header injection.
        $header_replyto_name = $user_name;
        $header_replyto_email = $user_email;

        // For the message content in an HTML email:
        // 1. Escape HTML special characters to prevent XSS if this HTML is ever viewed in a browser.
        // 2. Convert newlines (from textarea) to <br> tags for HTML display.
        $message_for_html_body = nl2br(htmlspecialchars($user_message, ENT_QUOTES, 'UTF-8'));

        // --- Construct Email Content ---
        $email_subject = $email_subject_prefix;
        if (!empty($display_user_name)) {
            $email_subject .= " from " . $display_user_name;
        }

        // Construct HTML Email Body using external template
        ob_start();
        include __DIR__ . '/templates/contact_email.php';
        $email_body = ob_get_clean();


        $mail = new PHPMailer(true);

        try {
            $mail->setFrom($server_mandated_from_email, $from_display_name);
            $mail->addAddress($recipient_email);
            $mail->addReplyTo($header_replyto_email, $header_replyto_name);
            $mail->isHTML(true);
            $mail->Subject = $email_subject;
            $mail->Body    = $email_body;
            $mail->AltBody = $user_message;

            $mail->send();
            $response['success'] = true;
            http_response_code(200);
        } catch (Exception $e) {
            error_log("HTML_EMAIL_MAILER - Mail sending FAILED. Reason: " . $mail->ErrorInfo, 0);
            $response['errors'][] = "Message could not be sent due to a server issue. Please try again.";
            $response['detailed_error_guess'] = $mail->ErrorInfo;
            http_response_code(500);
        }
    }
} else {
    $response['errors'][] = "Invalid request method.";
    http_response_code(405);
    // error_log("HTML_EMAIL_MAILER - Invalid request method.", 0);
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>