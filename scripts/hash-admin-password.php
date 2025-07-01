<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;

$userModel = new User($container->getPdo());

$adminEmail = 'admin@example.com'; // Replace with your admin email
$newPassword = 'password'; // Replace with your desired new password

$user = $userModel->findByEmail($adminEmail);

if ($user) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $container->getPdo()->prepare('UPDATE users SET password = :password WHERE id = :id');
    if ($stmt->execute(['password' => $hashedPassword, 'id' => $user['id']])) {
        echo "Admin password updated successfully.\n";
    } else {
        echo "Failed to update admin password.\n";
    }
} else {
    echo "Admin user not found.\n";
}
