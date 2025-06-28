<?php
$page_title = "Dashboard";

require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Models\User;
use App\Models\Package;

$db = new Database();
$pdo = $db->getConnection();

$userModel = new User($pdo);
$packageModel = new Package($pdo);

$totalUsers = $userModel->getTotalUsersCount();
$activePackages = $packageModel->getTotalActivePackagesCount();

include __DIR__ . '/header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Hello, Admin</h1>
        <p class="text-gray-500">Welcome to your Safe Haven Dutch Coaching CMS Dashboard.</p>
    </div>
    <div class="flex items-center text-gray-500">
        <span class="text-sm mr-2"><?= date('d M, Y') ?></span>
        <span class="material-icons">calendar_today</span>
    </div>
</header>

<section class="grid grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-50 p-4 rounded-xl flex items-start">
        <div class="bg-green-100 p-2 rounded-lg mr-4">
            <span class="material-icons text-green-600">group</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Users</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalUsers ?></p>
        </div>
    </div>
    <div class="bg-gray-50 p-4 rounded-xl flex items-start">
        <div class="bg-yellow-100 p-2 rounded-lg mr-4">
            <span class="material-icons text-yellow-600">redeem</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Active Packages</p>
            <p class="text-2xl font-bold text-gray-800"><?= $activePackages ?></p>
        </div>
    </div>
    <div class="bg-gray-50 p-4 rounded-xl flex items-start">
        <div class="bg-purple-100 p-2 rounded-lg mr-4">
            <span class="material-icons text-purple-600">shopping_cart</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Recent Purchases</p>
            <p class="text-2xl font-bold text-gray-800">[Dynamic Recent Purchases]</p>
        </div>
    </div>
</section>

<section class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">System Overview</h2>
        <button class="flex items-center text-sm text-gray-600 border border-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-100">
            Last 30 Days
            <span class="material-icons ml-1 text-sm">expand_more</span>
        </button>
    </div>
    <div class="relative h-64 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
        <!-- Placeholder for a chart or more detailed system metrics -->
        <p>Chart Placeholder: User Registrations vs. Package Purchases</p>
    </div>
</section>

<section>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Quick Actions</h2>
    </div>
    <div class="space-y-3">
        <a href="users/index.php" class="bg-gray-50 p-4 rounded-xl flex items-center justify-between hover:bg-gray-100 transition">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-4">
                    <span class="material-icons text-blue-500">group_add</span>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Manage Users</p>
                    <p class="text-xs text-gray-500">View, edit, or delete user accounts.</p>
                </div>
            </div>
            <span class="material-icons cursor-pointer">arrow_forward_ios</span>
        </a>
        <a href="packages/index.php" class="bg-gray-50 p-4 rounded-xl flex items-center justify-between hover:bg-gray-100 transition">
            <div class="flex items-center">
                <div class="bg-purple-100 p-2 rounded-lg mr-4">
                    <span class="material-icons text-purple-500">card_giftcard</span>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Manage Packages</p>
                    <p class="text-xs text-gray-500">Add, edit, or activate lesson packages.</p>
                </div>
            </div>
            <span class="material-icons cursor-pointer">arrow_forward_ios</span>
        </a>
        <a href="pages/index.php" class="bg-gray-50 p-4 rounded-xl flex items-center justify-between hover:bg-gray-100 transition">
            <div class="flex items-center">
                <div class="bg-green-100 p-2 rounded-lg mr-4">
                    <span class="material-icons text-green-500">description</span>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Manage Pages</p>
                    <p class="text-xs text-gray-500">Edit website content pages.</p>
                </div>
            </div>
            <span class="material-icons cursor-pointer">arrow_forward_ios</span>
        </a>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
