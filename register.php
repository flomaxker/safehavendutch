<?php
require_once __DIR__ . '/bootstrap.php';

use App\Models\Setting;

$error = '';

// Check if there's an error message from register-handler.php
if (isset($_GET['error'])) {
    $error_code = $_GET['error'];
    if ($error_code == 1) {
        $error = 'Please fill in all required fields.';
    } elseif ($error_code == 2) {
        $error = 'Email already registered. Please use a different email or log in.';
    } elseif ($error_code == 3) {
        $error = 'Passwords do not match.';
    } elseif ($error_code == 4) {
        $error = 'Registration failed. Please try again.';
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
    <title>Register</title>
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
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Create Your Account</h2>
            <p class="text-sm text-gray-600 text-center mb-8">Join us to start your journey.</p>
            
            <form action="register-handler.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="name">Full Name</label>
                    <input class="border border-gray-300 rounded-lg py-3 px-4 w-full text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500" id="name" name="name" placeholder="Your Name" type="text" required/>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">Email Address</label>
                    <input class="border border-gray-300 rounded-lg py-3 px-4 w-full text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500" id="email" name="email" placeholder="you@example.com" type="email" required/>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">Password</label>
                        <input class="border border-gray-300 rounded-lg py-3 px-4 w-full text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500" id="password" name="password" placeholder="••••••••" type="password" required/>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="confirm_password">Confirm Password</label>
                        <input class="border border-gray-300 rounded-lg py-3 px-4 w-full text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500" id="confirm_password" name="confirm_password" placeholder="••••••••" type="password" required/>
                    </div>
                </div>
                
                <?php if ($error): ?>
                    <p class="text-red-500 text-xs italic my-4 text-center"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300" type="submit">
                    Create Account
                </button>
            </form>
            <p class="text-center text-sm text-gray-600 mt-8">
                Already have an account? <a href="login.php" class="font-semibold text-primary-600 hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</body>
</html>
