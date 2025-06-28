<?php
require_once __DIR__ . '/bootstrap.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .register-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .register-card {
            background-color: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
            width: 100%;
            max-width: 28rem;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .logo-icon {
            font-size: 2.25rem;
            color: #1F2937;
            margin-right: 0.5rem;
        }
        .logo-text {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1F2937;
        }
        .form-input {
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 1rem;
            color: #374151;
            margin-bottom: 1.25rem;
        }
        .form-input:focus {
            outline: none;
            border-color: #60A5FA;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3);
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #4B5563;
            margin-bottom: 0.5rem;
        }
        .register-button {
            background-color: #3B82F6;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            width: 100%;
            text-align: center;
            transition: background-color 0.2s;
        }
        .register-button:hover {
            background-color: #2563EB;
        }
        .login-link {
            text-align: center;
            font-size: 0.875rem;
            color: #6B7280;
            margin-top: 2rem;
        }
        .login-link a {
            color: #3B82F6;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
<div class="register-container">
    <div class="register-card">
        <div class="logo-container">
            <img alt="Site logo" class="h-8 mr-2" src="/assets/images/default-logo.png"/>
            <span class="logo-text">CMS Register</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Create Your Account</h2>
        <p class="text-sm text-gray-600 text-center mb-8">Join us and start managing your services.</p>
        <form action="register-handler.php" method="POST">
            <div>
                <label class="form-label" for="name">Name</label>
                <input class="form-input" id="name" name="name" placeholder="Your Name" type="text" required/>
            </div>
            <div>
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" id="email" name="email" placeholder="you@example.com" type="email" required/>
            </div>
            <div>
                <label class="form-label" for="password">Password</label>
                <input class="form-input" id="password" name="password" placeholder="Enter your password" type="password" required/>
            </div>
            <div>
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input class="form-input" id="confirm_password" name="confirm_password" placeholder="Confirm your password" type="password" required/>
            </div>
            <button class="register-button" type="submit">
                Register
            </button>
            <?php if ($error): ?>
                <p class="text-red-500 text-xs italic mt-4 text-center"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
        <p class="login-link">
            Already have an account? <a href="login.php">Sign in</a>
        </p>
    </div>
</div>
</body>
</html>