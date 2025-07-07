<?php
define('USER_DASHBOARD_ACTIVE', true);

require_once __DIR__ . '/bootstrap.php';

use App\Models\User;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?error=unauthorized');
    exit();
}

$userModel = $container->getUserModel();

// Get the user's role and redirect if admin
$loggedInUser = $userModel->find($_SESSION['user_id']);
if ($loggedInUser && $loggedInUser['role'] === 'admin') {
    header('Location: /admin/index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $loggedInUser['name'] ?? 'User';
$user_euro_balance = $userModel->getEuroBalance($user_id);

$csp_policy = "default-src 'self'; ";
$csp_policy .= "script-src 'self' 'unsafe-eval' 'nonce-{$nonce}' https://cdn.jsdelivr.net https://cdn.tiny.cloud https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js https://cdnjs.cloudflare.com; ";
$csp_policy .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css https://fonts.googleapis.com https://cdnjs.cloudflare.com; ";
$csp_policy .= "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; ";
$csp_policy .= "img-src 'self' data: https:; ";
$csp_policy .= "connect-src 'self' https://*.tinymce.com https://sp.tinymce.com; ";
$csp_policy .= "frame-src 'self' https://*.tinymce.com; ";
$csp_policy .= "worker-src 'self' blob:;";
header("Content-Security-Policy: " . $csp_policy);

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?error=unauthorized');
    exit();
}

// User specific site settings (can be fetched from DB if needed)
$site_name = "User Dashboard";
$site_logo = "/assets/images/default-logo.png"; // Generic logo for user dashboard

// Define user navigation links
$nav_groups = [
    [
        'title' => 'Main Navigation',
        'links' => [
            ['title' => 'Dashboard', 'url' => '/dashboard.php', 'icon' => 'home'],
            ['title' => 'My Bookings', 'url' => '/my_bookings.php', 'icon' => 'event_note'],
            ['title' => 'Book a Lesson', 'url' => '/book_lesson.php', 'icon' => 'school'],
            ['title' => 'Purchase History', 'url' => '/purchase_history.php', 'icon' => 'history'],
        ]
    ],
    [
        'title' => 'My Account',
        'links' => [
            ['title' => 'My Children', 'url' => '/my_children.php', 'icon' => 'child_care'],
        ]
    ],
    [
        'title' => 'Resources & Communication',
        'links' => [
            ['title' => 'My Progress', 'url' => '/my_progress.php', 'icon' => 'trending_up'],
            ['title' => 'My Notes', 'url' => '/my_notes.php', 'icon' => 'description'],
            ['title' => 'My Attachments', 'url' => '/my_attachments.php', 'icon' => 'attachment'],
            ['title' => 'My Messages', 'url' => '/my_messages.php', 'icon' => 'message'],
        ]
    ],
];

// Determine active link for styling
$current_uri = $_SERVER['REQUEST_URI'];

function is_active_link($link_url, $current_uri) {
    // Exact match for dashboard or when URL is '/'
    if ($link_url === '/dashboard.php' || $link_url === '/') {
        return $current_uri === '/dashboard.php' || $current_uri === '/';
    }
    // Match starting path for other URLs
    return strpos($current_uri, $link_url) === 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo $page_title ?? 'Dashboard'; ?> | <?php echo $site_name; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
        .material-icons {
            font-size: 20px;
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
            <div class="flex items-center mb-4">
                <img alt="Site logo" class="h-8 mr-2" src="<?php echo $site_logo; ?>"/>
                <span class="text-2xl font-bold text-gray-800">User Panel</span>
            </div>
            <div class="mb-8 text-center">
                <p class="text-lg font-semibold text-gray-800">Welcome, <?php echo htmlspecialchars($user_name); ?>!</p>
                <p class="text-sm text-gray-600">Balance: €<?php echo htmlspecialchars(number_format($user_euro_balance / 100, 2)); ?></p>
            </div>
            <nav>
                <ul class="space-y-2">
                    <?php foreach ($nav_groups as $index => $group): ?>
                        <?php 
                            $is_group_active = false;
                            foreach ($group['links'] as $link) {
                                if (is_active_link($link['url'], $current_uri)) {
                                    $is_group_active = true;
                                    break;
                                }
                            }
                            $menu_id = 'user_menu_item_' . $index;
                        ?>
                        <li x-data="{ open: JSON.parse(localStorage.getItem('<?php echo $menu_id; ?>')) ?? <?php echo $is_group_active ? 'true' : 'false'; ?> }" x-init="$watch('open', val => localStorage.setItem('<?php echo $menu_id; ?>', val))">
                            <a href="#" @click.prevent="open = !open" class="flex items-center justify-between text-gray-600 hover:text-gray-900 font-medium p-2 rounded-lg transition-colors duration-200 <?php echo $is_group_active ? 'bg-gray-100 text-gray-900' : ''; ?>">
                                <div class="flex items-center">
                                    <span class="material-icons mr-3"><?php echo $group['links'][0]['icon']; ?></span> <!-- Using first icon of the group as group icon -->
                                    <span><?php echo $group['title']; ?></span>
                                </div>
                                <span class="material-icons transition-transform" :class="{ 'rotate-180': open }">expand_more</span>
                            </a>
                            <ul x-show="open" x-transition class="pl-4 mt-2 space-y-2">
                                <?php foreach ($group['links'] as $link): ?>
                                    <?php $is_active = is_active_link($link['url'], $current_uri); ?>
                                    <li>
                                        <a href="<?php echo $link['url']; ?>" class="flex items-center text-gray-600 hover:text-gray-900 font-medium p-2 rounded-lg transition-colors duration-200 <?php echo $is_active ? 'bg-gray-200 text-gray-900' : ''; ?>">
                                            <span class="material-icons mr-3"><?php echo $link['icon']; ?></span>
                                            <span><?php echo $link['title']; ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
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
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar" class="w-8 h-8 rounded-full">
                    <span><?php echo htmlspecialchars($user_name); ?></span>
                    <span class="material-icons text-sm" x-bind:class="{ 'rotate-180': open }">expand_more</span>
                </button>

                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    <a href="/my_profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                    <a href="/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log out</a>
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