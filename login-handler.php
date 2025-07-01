<?php

require_once 'bootstrap.php';

use App\Models\User;

// Rate limiting constants
const MAX_LOGIN_ATTEMPTS = 5;
const LOCKOUT_TIME = 300; // 5 minutes

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    header('Location: /login.php?error=1');
    exit();
}

try {
    $userModel = new User($container->getPdo());
    $user = $userModel->findByEmail($email);

    // Check for rate limiting
    if ($user) {
        $attempts = $userModel->getFailedLoginAttempts($user['id']);
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $last_attempt_time = $userModel->getLastFailedLoginAttemptTime($user['id']);
            if (time() - strtotime($last_attempt_time) < LOCKOUT_TIME) {
                header('Location: /login.php?error=locked');
                exit();
            }
        }
    }

    if ($user && password_verify($password, $user['password'])) {
        $userModel->recordLoginAttempt($user['id'], true);
        $userModel->updateLastLogin($user['id']);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: /admin/index.php');
        } else {
            header('Location: /dashboard.php');
        }
        exit();
    } else {
        if ($user) {
            $userModel->recordLoginAttempt($user['id'], false);
        }
        header('Location: /login.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    // Log the error and redirect to a generic error page or login with error
    error_log("Login error: " . $e->getMessage());
    header('Location: /login.php?error=2'); // Generic database error
    exit();
}
