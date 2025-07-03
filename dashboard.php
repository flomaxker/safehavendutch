<?php
$page_title = "Dashboard";
require_once __DIR__ . '/user_header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userModel = $container->getUserModel();
$user_id = $_SESSION['user_id'];
$user_euro_balance = $userModel->getEuroBalance($user_id);
$user_email = $_SESSION['user_email'] ?? 'Guest';

$page_title = "Dashboard";

?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, <?php echo htmlspecialchars($user_email); ?>!</h1>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 w-12 h-12 flex items-center justify-center rounded-xl mr-4">
                    <span class="material-icons text-3xl">account_balance_wallet</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Your Euro Balance</p>
                    <p class="text-3xl font-bold text-gray-900">€<?php echo htmlspecialchars(number_format($user_euro_balance / 100, 2)); ?></p>
                </div>
            </div>
        </div>
        <!-- More dashboard widgets can be added here -->
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-2xl shadow-lg mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="packages.php" class="flex flex-col items-center justify-center p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors duration-200">
                <span class="material-icons text-3xl text-gray-600 mb-2">shopping_cart</span>
                <span class="text-sm font-medium text-center text-gray-700">Top Up Balance</span>
            </a>
            <a href="purchase_history.php" class="flex flex-col items-center justify-center p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors duration-200">
                <span class="material-icons text-3xl text-gray-600 mb-2">history</span>
                <span class="text-sm font-medium text-center text-gray-700">View Purchase History</span>
            </a>
        </div>
    </div>
</div>

