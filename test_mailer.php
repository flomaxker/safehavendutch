<?php

require_once __DIR__ . '/bootstrap.php';

use App\Mail\Mailer;

$mailer = $container->getMailer();

$toEmail = 'test@example.com'; // Replace with a real email for testing
$toName = 'Test User';
$subject = 'Test Booking Confirmation';

$templateData = [
    'userName' => 'Test User',
    'lessonTitle' => 'Introduction to PHP',
    'teacherName' => 'John Doe',
    'lessonStartTime' => '2025-07-01 10:00',
    'lessonEndTime' => '2025-07-01 11:00',
    'lessonLocation' => 'Online',
];

$body = $mailer->loadTemplate(__DIR__ . '/templates/emails/booking_confirmation.php', $templateData);

if ($mailer->send($toEmail, $toName, $subject, $body)) {
    echo "Test email sent successfully!\n";
} else {
    echo "Failed to send test email. Check error logs.\n";
}

