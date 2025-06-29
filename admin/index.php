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

// Fetch quick actions order for the logged-in admin
$adminUserId = $_SESSION['user_id']; // Assuming admin user ID is stored in session
$adminUser = $userModel->findByEmail($_SESSION['user_email']); // Fetch full user data including quick_actions_order
$quickActionsOrder = json_decode($adminUser['quick_actions_order'] ?? '[]', true); // Decode JSON, default to empty array

// Define all possible quick actions with their properties
$allQuickActions = [
    '/admin/users/index.php' => ['title' => 'Manage Users', 'icon' => 'group_add', 'description' => 'View, edit, or delete user accounts.'],
    '/admin/packages/index.php' => ['title' => 'Manage Packages', 'icon' => 'card_giftcard', 'description' => 'Add, edit, or activate lesson packages.'],
    '/admin/pages/index.php' => ['title' => 'Manage Pages', 'icon' => 'description', 'description' => 'Edit website content pages.'],
    '/admin/children/index.php' => ['title' => 'Manage Children', 'icon' => 'child_care', 'description' => 'Manage child profiles.'],
    '/admin/bookings/index.php' => ['title' => 'Manage Bookings', 'icon' => 'event_note', 'description' => 'View and manage lesson bookings.'],
    '/admin/blog/posts/index.php' => ['title' => 'Manage Blog Posts', 'icon' => 'article', 'description' => 'Create and edit blog posts.'],
    '/admin/blog/categories/index.php' => ['title' => 'Manage Blog Categories', 'icon' => 'label', 'description' => 'Organize blog content with categories.'],
    '/admin/emails/index.php' => ['title' => 'Manage Email Templates', 'icon' => 'email', 'description' => 'Edit system email templates.'],
    '/admin/settings/index.php' => ['title' => 'System Settings', 'icon' => 'settings_applications', 'description' => 'Configure system-wide settings.'],
    '/admin/files/index.php' => ['title' => 'File Management', 'icon' => 'folder', 'description' => 'Manage uploaded files.'],
    '/admin/gdpr/index.php' => ['title' => 'GDPR Tools', 'icon' => 'privacy_tip', 'description' => 'Tools for GDPR compliance.'],
    '/admin/reports/index.php' => ['title' => 'View Reports', 'icon' => 'bar_chart', 'description' => 'Access system reports and analytics.'],
    '/admin/teachers/index.php' => ['title' => 'Manage Teachers', 'icon' => 'person', 'description' => 'Manage teacher profiles and availability.'],
    '/admin/rate_limiting/index.php' => ['title' => 'Rate Limiting', 'icon' => 'speed', 'description' => 'Configure rate limiting settings.'],
];

// If quick_actions_order is empty, use a default set
if (empty($quickActionsOrder)) {
    $quickActionsOrder = [
        '/admin/users/index.php',
        '/admin/packages/index.php',
        '/admin/pages/index.php',
        '/admin/bookings/index.php',
        '/admin/blog/posts/index.php',
    ];
}

// Filter and order quick actions based on $quickActionsOrder
$displayQuickActions = [];
foreach ($quickActionsOrder as $url) {
    if (isset($allQuickActions[$url])) {
        $displayQuickActions[$url] = $allQuickActions[$url];
    }
}

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

<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
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
            <p class="text-sm text-gray-500">Active Packages (Euros)</p>
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
    <div id="quick-actions-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($displayQuickActions as $url => $action): ?>
            <a href="<?= $url ?>" class="bg-gray-50 p-4 rounded-xl flex items-center justify-between hover:bg-gray-100 transition">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 rounded-lg mr-4">
                        <span class="material-icons text-blue-500"><?= $action['icon'] ?></span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800"><?= $action['title'] ?></p>
                        <p class="text-xs text-gray-500"><?= $action['description'] ?></p>
                    </div>
                </div>
                <span class="material-icons cursor-grab">drag_indicator</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quickActionsList = document.getElementById('quick-actions-list');
        const adminUserId = <?= $_SESSION['user_id'] ?>;

        if (quickActionsList) {
            new Sortable(quickActionsList, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                handle: '.cursor-grab',
                onEnd: function (evt) {
                    const newOrder = Array.from(evt.to.children).map(item => item.getAttribute('href'));
                    
                    // Send the new order to the server
                    fetch('save_quick_actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ order: newOrder, userId: adminUserId }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            console.log('Quick actions order saved successfully!');
                        } else {
                            console.error('Error saving quick actions order:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Network error saving quick actions order:', error);
                    });
                },
            });
        }
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
