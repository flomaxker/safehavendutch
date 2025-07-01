<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Mail\Mailer;

// Configuration
const INACTIVITY_PERIOD_MONTHS = 24;
const ADMIN_EMAIL = 'admin@safehavendutch.com'; // Replace with the actual admin email

$userModel = new User($container->getPdo());
$mailer = $container->getMailer();

// Find inactive users
$inactive_users = $userModel->findInactiveUsers(INACTIVITY_PERIOD_MONTHS);

if (empty($inactive_users)) {
    echo "No inactive users found.\n";
    exit;
}

// Prepare and send the email
$subject = 'Inactive User Report';
$body = "<h1>Inactive User Report</h1>";
$body .= "<p>The following users have been inactive for over " . INACTIVITY_PERIOD_MONTHS . " months and may be candidates for data anonymization:</p>";
$body .= "<ul>";
foreach ($inactive_users as $user) {
    $body .= "<li>" . htmlspecialchars($user['email']) . " (Last Login: " . ($user['last_login_at'] ?: 'Never') . ")</li>";
}
$body .= "</ul>";
$body .= "<p>Please review these accounts in the GDPR admin panel.</p>";

try {
    $mailer->send(ADMIN_EMAIL, $subject, $body);
    echo "Inactive user report sent to " . ADMIN_EMAIL . "\n";
} catch (Exception $e) {
    echo "Failed to send email: " . $e->getMessage() . "\n";
}
