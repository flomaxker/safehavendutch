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
    $database = new Database();
    $pdo = $database->getConnection();
    $userModel = new User($pdo);

    $user = $userModel->findByEmail($email);

    // In production, use password_verify() with hashed passwords
    // For MVP, assuming plain text password for now based on admin/login.php
    // You should replace this with password_verify($password, $user['password'])
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_credits'] = $userModel->getCredits($user['id']);
        $_SESSION['user_logged_in'] = true;

        // Redirect based on user role if applicable, otherwise to dashboard
        // Assuming 'role' column exists in 'users' table
        if (isset($user['role']) && $user['role'] === 'admin') {
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