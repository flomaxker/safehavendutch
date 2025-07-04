<?php
require_once __DIR__ . '/bootstrap.php';

$csp_policy = "default-src 'self'; ";
$csp_policy .= "script-src 'self' 'unsafe-eval' 'nonce-{$nonce}' https://cdn.jsdelivr.net https://cdn.tiny.cloud https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js https://cdnjs.cloudflare.com; ";
$csp_policy .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css https://fonts.googleapis.com https://cdnjs.cloudflare.com; ";
$csp_policy .= "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; ";
$csp_policy .= "img-src 'self' data: https:; ";
$csp_policy .= "connect-src 'self' https://*.tinymce.com https://sp.tinymce.com; ";
$csp_policy .= "frame-src 'self' https://*.tinymce.com; ";
$csp_policy .= "worker-src 'self' blob:;";
header("Content-Security-Policy: " . $csp_policy);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to the main login page if not an admin
    header('Location: /login.php?error=unauthorized');
    exit();
}

$userModel = $container->getUserModel();
$loggedInAdmin = $userModel->find($_SESSION['user_id']);
$admin_name = $loggedInAdmin['name'] ?? 'Admin';

// Admin specific site settings
$site_name = "Admin Panel";
$site_logo = "/assets/images/default-logo.png"; // Generic logo for admin
$nav_links = [
    ['title' => 'Dashboard', 'url' => '/admin/index.php', 'icon' => 'home'],
    ['title' => 'User Management', 'icon' => 'group', 'children' => [
        ['title' => 'Users', 'url' => '/admin/users/index.php', 'icon' => 'group'],
        ['title' => 'Children', 'url' => '/admin/children/index.php', 'icon' => 'child_care'],
    ]],
    ['title' => 'Service Management', 'icon' => 'redeem', 'children' => [
        ['title' => 'Packages', 'url' => '/admin/packages/index.php', 'icon' => 'card_giftcard'],
        ['title' => 'Bookings', 'url' => '/admin/bookings/index.php', 'icon' => 'event_note'],
        ['title' => 'Lessons', 'url' => '/admin/lessons/index.php', 'icon' => 'school'],
        ['title' => 'Teachers', 'url' => '/admin/teachers/index.php', 'icon' => 'person'],
    ]],
    ['title' => 'Content Management', 'icon' => 'description', 'children' => [
        ['title' => 'Pages', 'url' => '/admin/pages/index.php', 'icon' => 'description'],
        ['title' => 'Blog Posts', 'url' => '/admin/blog/posts/index.php', 'icon' => 'article'],
        ['title' => 'Blog Categories', 'url' => '/admin/blog/categories/index.php', 'icon' => 'label'],
    ]],
    ['title' => 'System & Tools', 'icon' => 'build', 'children' => [
        ['title' => 'Email Templates', 'url' => '/admin/emails/index.php', 'icon' => 'email'],
        ['title' => 'System Settings', 'url' => '/admin/settings/index.php', 'icon' => 'settings_applications'],
        ['title' => 'File Management', 'url' => '/admin/files/index.php', 'icon' => 'folder'],
        ['title' => 'GDPR Tools', 'url' => '/admin/gdpr/index.php', 'icon' => 'privacy_tip'],
        ['title' => 'Reports', 'url' => '/admin/reports/index.php', 'icon' => 'bar_chart'],
        ['title' => 'Rate Limiting', 'url' => '/admin/rate_limiting/index.php', 'icon' => 'speed'],
    ]],
];

// Determine active link for styling
$current_uri = $_SERVER['REQUEST_URI'];

function is_active_link($link_url, $current_uri) {
    // Exact match for dashboard or when URL is '/'
    if ($link_url === '/admin/index.php' || $link_url === '/admin/') {
        return $current_uri === '/admin/index.php' || $current_uri === '/admin/';
    }
    // Match starting path for other URLs, as long as it's not just the root
    return strpos($current_uri, $link_url) === 0 && strlen($current_uri) > strlen('/admin/');
}

function is_active_parent($children, $current_uri) {
    foreach ($children as $child) {
        if (is_active_link($child['url'], $current_uri)) {
            return true;
        }
    }
    return false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo $page_title ?? 'Admin'; ?> | <?php echo $site_name; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
        .material-icons {
            font-size: 20px;
        }
        .quick-action-item {
            user-select: none !important; /* Prevent text selection */
            -webkit-user-select: none !important; /* Safari */
            -moz-user-select: none !important; /* Firefox */
            -ms-user-select: none !important; /* IE 10+ */
            cursor: default !important;
        }
        .quick-action-list .sortable-ghost {
            opacity: 0.2;
        }
        .quick-action-list.sortable-drag {
            border: 2px dashed #9CA3AF; /* Dashed border when dragging over */
            background-color: #F3F4F6; /* Light background when dragging over */
        }
        .quick-action-list.sortable-empty {
            border: 2px dashed #D1D5DB; /* Dashed border for empty list */
            background-color: #F9FAFB; /* Light background for empty list */
        }
        #cropping-modal .cropper-container {
            max-width: 100%;
        }
        #cropping-modal #image-to-crop {
            max-height: 60vh;
        }
        .chart-loader {
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -20px;
            margin-left: -20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="flex flex-col md:flex-row h-screen">
    <!-- Mobile menu button -->
    <button id="mobile-menu-button" class="md:hidden p-4 focus:outline-none focus:bg-gray-200">
        <i class="fas fa-bars text-gray-600 text-2xl"></i>
    </button>

    <aside id="sidebar" class="w-full md:w-64 bg-white p-8 border-r border-gray-200 flex-col justify-between h-screen overflow-y-auto fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-200 ease-in-out z-50 md:flex">
        <div>
            <div class="flex items-center mb-12">
                <img alt="Site logo" class="h-8 mr-2" src="<?php echo $site_logo; ?>"/>
                <span class="text-2xl font-bold text-gray-800">Admin Panel</span>
            </div>
            <nav>
                <ul class="space-y-2">
                    <?php foreach ($nav_links as $index => $link): ?>
                        <?php if (isset($link['children']) && !empty($link['children'])): ?>
                            <?php 
                                $is_parent_active = is_active_parent($link['children'], $current_uri);
                                $menu_id = 'menu_item_' . $index;
                            ?>
                            <li x-data="{ open: JSON.parse(localStorage.getItem('<?php echo $menu_id; ?>')) ?? <?php echo $is_parent_active ? 'true' : 'false'; ?> }" x-init="$watch('open', val => localStorage.setItem('<?php echo $menu_id; ?>', val))">
                                <a href="#" @click.prevent="open = !open" class="flex items-center justify-between text-gray-600 hover:text-gray-900 font-medium p-2 rounded-lg transition-colors duration-200 <?php echo $is_parent_active ? 'bg-gray-100 text-gray-900' : ''; ?>">
                                    <div class="flex items-center">
                                        <span class="material-icons mr-3"><?php echo $link['icon']; ?></span>
                                        <span><?php echo $link['title']; ?></span>
                                    </div>
                                    <span class="material-icons transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                                </a>
                                <ul x-show="open" x-transition class="pl-4 mt-2 space-y-2">
                                    <?php foreach ($link['children'] as $child): ?>
                                        <?php $is_child_active = is_active_link($child['url'], $current_uri); ?>
                                        <li>
                                            <a href="<?php echo $child['url']; ?>" class="flex items-center text-gray-600 hover:text-gray-900 font-medium p-2 rounded-lg transition-colors duration-200 <?php echo $is_child_active ? 'bg-gray-200 text-gray-900' : ''; ?>">
                                                <span class="material-icons mr-3"><?php echo $child['icon']; ?></span>
                                                <span><?php echo $child['title']; ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else: ?>
                            <?php $is_active = is_active_link($link['url'], $current_uri); ?>
                            <li>
                                <a href="<?php echo $link['url']; ?>" class="flex items-center text-gray-600 hover:text-gray-900 font-medium p-2 rounded-lg transition-colors duration-200 <?php echo $is_active ? 'bg-gray-200 text-gray-900' : ''; ?>">
                                    <span class="material-icons mr-3"><?php echo $link['icon']; ?></span>
                                    <span><?php echo $link['title']; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Overlay for mobile menu -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black opacity-0 md:hidden z-40 pointer-events-none transition-opacity duration-200 ease-in-out"></div>

    <main class="w-full md:flex-1 p-8 h-screen overflow-y-auto">
        <div class="flex justify-end mb-6">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=random&color=fff" alt="Admin Avatar" class="w-8 h-8 rounded-full">
                    <span><?php echo htmlspecialchars($admin_name); ?></span>
                    <span class="material-icons text-sm" x-bind:class="{ 'rotate-180': open }">expand_more</span>
                </button>

                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    <a href="/admin/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log out</a>
                </div>
            </div>
        </div>

<script nonce="<?php echo $nonce; ?>">
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', function () {
                sidebar.classList.toggle('-translate-x-full');
                mobileMenuOverlay.classList.toggle('opacity-0');
                mobileMenuOverlay.classList.toggle('pointer-events-none');
            });
        }

        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', function () {
                sidebar.classList.add('-translate-x-full');
                mobileMenuOverlay.classList.add('opacity-0');
                mobileMenuOverlay.classList.add('pointer-events-none');
            });
        }
    });
</script>