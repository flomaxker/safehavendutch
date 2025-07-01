<?php
require_once __DIR__ . '/bootstrap.php';

// Redirect if user is already logged in
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: /admin/index.php');
    } else {
        header('Location: /dashboard.php');
    }
    exit;
}

use App\Models\Setting;

$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_message = 'Invalid email or password.';
    } else {
        $error_message = 'An unexpected error occurred. Please try again.';
    }
}

$settingModel = new Setting($container->getPdo());
$settings = $settingModel->getAllSettings();
$siteLogo = $settings['site_logo'] ?? '/assets/images/default-logo.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-2xl rounded-3xl p-8">
            <div class="flex justify-center mb-8">
                <a href="/">
                    <img alt="Site logo" class="h-10" src="<?= htmlspecialchars($siteLogo) ?>"/>
                </a>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Welcome Back!</h2>
            <p class="text-sm text-gray-600 text-center mb-8">Please enter your details to sign in.</p>
            
            <form action="login-handler.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">Email Address</label>
                    <input class="border border-gray-300 rounded-lg py-3 px-4 w-full text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500" id="email" name="email" placeholder="you@example.com" type="text" required/>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">Password</label>
                    <input class="border border-gray-300 rounded-lg py-3 px-4 w-full text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500" id="password" name="password" placeholder="••••••••" type="password" required/>
                </div>
                
                <div class="text-right mb-6">
                    <a href="#" class="text-sm font-semibold text-gray-600 hover:text-primary-600">Forgot password?</a>
                </div>

                <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
                </div>
                <?php endif; ?>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300" type="submit">
                    Sign In
                </button>
            </form>
            <p class="text-center text-sm text-gray-600 mt-8">
                Don't have an account? <a href="register.php" class="font-semibold text-primary-600 hover:underline">Sign up</a>
            </p>
        </div>
    </div>
</body>
</html>