<?php

require_once __DIR__ . '/bootstrap.php';

use App\Database\Database;
use App\Models\User;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    if (!$name || !$email || !$password || !$confirm_password) {
        header('Location: /register.php?error=1'); // Missing fields
        exit();
    }

    if ($password !== $confirm_password) {
        header('Location: /register.php?error=3'); // Passwords do not match
        exit();
    }

    try {
        $database = new Database();
        $pdo = $database->getConnection();
        $userModel = new User($pdo);

        // Check if email already exists
        if ($userModel->findByEmail($email)) {
            header('Location: /register.php?error=2'); // Email already registered
            exit();
        }

        // Hash the password before storing it
        // In a real application, use password_hash() for secure password storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Create the user (assuming a create method in User model)
        // You'll need to add a create method to your User model if it doesn't exist
        $userId = $userModel->create($name, $email, $hashed_password, 'member');

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'member'; // Set user role in session
            $_SESSION['user_credits'] = 0; // New users start with 0 credits
            header('Location: /dashboard.php');
            exit();
        } else {
            header('Location: /register.php?error=4'); // Registration failed
            exit();
        }

    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        header('Location: /register.php?error=4'); // Generic registration error
        exit();
    }
}