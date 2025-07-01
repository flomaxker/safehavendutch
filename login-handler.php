<?php

require_once 'bootstrap.php';

use App\Database\Database;
use App\Models\User;

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    header('Location: /login.php?error=1');
    exit();
}

try {
    $userModel = new User($container->getPdo());
    $userModel = new User($container->getPdo());
    $user = $userModel->findByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: /admin/index.php');
        } else {
            header('Location: /dashboard.php');
        }
        exit();
    } else {
        header('Location: /login.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    // Log the error and redirect to a generic error page or login with error
    error_log("Login error: " . $e->getMessage());
    header('Location: /login.php?error=2'); // Generic database error
    exit();
}