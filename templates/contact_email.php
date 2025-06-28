<?php
/**
 * Contact form email template.
 * Variables expected:
 * - $email_subject
 * - $display_user_name
 * - $user_email
 * - $display_user_email
 * - $message_for_html_body
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($email_subject, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
        h2 { color: #2c3e50; margin-top:0; }
        p { margin-bottom: 10px; }
        strong { color: #34495e; }
        .message-content { padding: 15px; background-color: #ffffff; border: 1px solid #eee; border-radius: 4px; margin-top: 5px; white-space: pre-wrap; word-wrap: break-word; }
        hr { border: 0; height: 1px; background: #ddd; margin: 20px 0; }
        .footer { font-size: 0.9em; color: #7f8c8d; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>New Contact Form Submission</h2>
    <p>You have received a new message from your website contact form:</p>
    <hr>
    <p><strong>Name:</strong> <?= $display_user_name ?></p>
    <p><strong>Email:</strong> <a href="mailto:<?= rawurlencode($user_email) ?>"><?= $display_user_email ?></a></p>
    <p><strong>Message:</strong></p>
    <div class="message-content"><?= $message_for_html_body ?></div>
    <hr>
    <p class="footer"><em>Sent via Website Contact Form</em></p>
</div>
</body>
</html>

