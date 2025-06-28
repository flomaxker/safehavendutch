<?php
require_once __DIR__ . '/bootstrap.php';

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$user_email = $_SESSION['user_email'] ?? 'Guest';
// Placeholder for user's credit balance - this would be fetched from the database
$user_euro_balance = $_SESSION['user_euro_balance'] ?? 0;

$page_title = "Dashboard";
include 'header.php'; // Assuming header.html contains the common header for user pages
?>

<div class="p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Welcome, <?php echo htmlspecialchars($user_email); ?>!</h1>
    <p class="text-gray-600 mb-6">This is your personal dashboard. Here you can manage your services and view your details.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-blue-50 p-6 rounded-xl flex items-start">
            <div class="bg-blue-100 p-2 rounded-lg mr-4">
                <span class="material-icons text-blue-600">account_balance_wallet</span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Your Euro Balance</p>
                <p class="text-2xl font-bold text-gray-800">€<?php echo htmlspecialchars(number_format($user_euro_balance / 100, 2)); ?></p>
            </div>
        </div>
        <!-- More dashboard widgets can be added here -->
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="packages.php" class="bg-gray-100 p-6 rounded-xl flex items-center hover:bg-gray-200 transition">
                <span class="material-icons text-gray-600 mr-4">shopping_cart</span>
                <span class="text-lg font-medium text-gray-800">Top Up Balance</span>
            </a>
            <a href="purchase_history.php" class="bg-gray-100 p-6 rounded-xl flex items-center hover:bg-gray-200 transition">
                <span class="material-icons text-gray-600 mr-4">history</span>
                <span class="text-lg font-medium text-gray-800">View Purchase History</span>
            </a>
        </div>
    </div>
</div>

<?php
include 'footer.php'; // Assuming footer.html contains the common footer
?>