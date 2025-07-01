<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;

$userModel = new User($container->getPdo());

$adminEmail = 'admin@safehaven.com';
$adminPassword = 'securepassword'; // Please change this after logging in

// Check if user already exists
if ($userModel->findByEmail($adminEmail)) {
    echo "Admin user with email {$adminEmail} already exists. No action taken.\n";
    exit;
}

$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

try {
    $userModel->create('Admin User', $adminEmail, $hashedPassword, 'admin');
    echo "Successfully created a new admin user.\n";
    echo "Email: " . $adminEmail . "\n";
    echo "Password: " . $adminPassword . "\n";
} catch (\Exception $e) {
    echo "Failed to create admin user: " . $e->getMessage() . "\n";
}
