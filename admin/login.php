<?php
require_once __DIR__ . '/bootstrap.php';

use App\Database\Database;
use App\Models\User;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = new Database();
    $pdo = $db->getConnection();
    $userModel = new User($pdo);

    // In production, retrieve hashed password from DB and use password_verify()
    $adminUser = $userModel->findByUsername($username);

    if ($adminUser && $adminUser['role'] === 'admin' && password_verify($password, $adminUser['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_id'] = $adminUser['id'];
        $_SESSION['user_email'] = $adminUser['email'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Admin panel background */
        }
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem; /* Equivalent to p-4 in Tailwind */
        }
        .login-card {
            background-color: white;
            border-radius: 1.5rem; /* rounded-3xl in Tailwind */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); /* shadow-2xl in Tailwind */
            padding: 2rem; /* p-8 in Tailwind */
            width: 100%;
            max-width: 28rem; /* max-w-md in Tailwind */
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem; /* mb-8 in Tailwind */
        }
        .logo-icon {
            font-size: 2.25rem; /* text-3xl in Tailwind */
            color: #1F2937; /* gray-800 in Tailwind */
            margin-right: 0.5rem; /* mr-2 in Tailwind */
        }
        .logo-text {
            font-size: 1.875rem; /* text-3xl in Tailwind */
            font-weight: 700; /* font-bold in Tailwind */
            color: #1F2937; /* gray-800 in Tailwind */
        }
        .form-input {
            border: 1px solid #D1D5DB; /* border-gray-300 in Tailwind */
            border-radius: 0.5rem; /* rounded-lg in Tailwind */
            padding: 0.75rem 1rem; /* py-3 px-4 in Tailwind */
            width: 100%;
            font-size: 1rem; /* text-base in Tailwind */
            color: #374151; /* gray-700 in Tailwind */
            margin-bottom: 1.25rem; /* mb-5 in Tailwind */
        }
        .form-input:focus {
            outline: none;
            border-color: #60A5FA; /* border-blue-400 in Tailwind */
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3); /* focus:ring focus:ring-blue-200 in Tailwind */
        }
        .form-label {
            display: block;
            font-size: 0.875rem; /* text-sm in Tailwind */
            font-weight: 600; /* font-semibold in Tailwind */
            color: #4B5563; /* gray-700 in Tailwind */
            margin-bottom: 0.5rem; /* mb-2 in Tailwind */
        }
        .login-button {
            background-color: #3B82F6; /* bg-blue-500 in Tailwind */
            color: white;
            font-size: 1rem; /* text-base in Tailwind */
            font-weight: 700; /* font-bold in Tailwind */
            border-radius: 0.5rem; /* rounded-lg in Tailwind */
            padding: 0.75rem 1rem; /* py-3 px-4 in Tailwind */
            width: 100%;
            text-align: center;
            transition: background-color 0.2s;
        }
        .login-button:hover {
            background-color: #2563EB; /* hover:bg-blue-600 in Tailwind */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
<div class="login-container">
    <div class="login-card">
        <div class="logo-container">
            <img alt="Site logo" class="h-8 mr-2" src="/assets/images/default-logo.png"/>
            <span class="logo-text">Admin Login</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Welcome Back!</h2>
        <p class="text-sm text-gray-600 text-center mb-8">Please enter your details to sign in.</p>
        <form action="login.php" method="POST">
            <div>
                <label class="form-label" for="username">Username</label>
                <input class="form-input" id="username" name="username" placeholder="admin" type="text"/>
            </div>
            <div>
                <label class="form-label" for="password">Password</label>
                <input class="form-input" id="password" name="password" placeholder="Enter your password" type="password"/>
            </div>
            <button class="login-button" type="submit">
                Sign In
            </button>
            <?php if ($error): ?>
                <p class="text-red-500 text-xs italic mt-4 text-center"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>
</body>
</html>