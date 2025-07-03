<?php
define('USER_DASHBOARD_ACTIVE', true);

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

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?error=unauthorized');
    exit();
}

// User specific site settings (can be fetched from DB if needed)
$site_name = "User Dashboard";
$site_logo = "/assets/images/default-logo.png"; // Generic logo for user dashboard

// Define user navigation links
$nav_links = [
    ['title' => 'Dashboard', 'url' => '/dashboard.php', 'icon' => 'home'],
    ['title' => 'My Children', 'url' => '/my_children.php', 'icon' => 'child_care'],
    ['title' => 'My Bookings', 'url' => '/my_bookings.php', 'icon' => 'event_note'],
    ['title' => 'Book a Lesson', 'url' => '/book_lesson.php', 'icon' => 'school'],
    ['title' => 'Purchase History', 'url' => '/purchase_history.php', 'icon' => 'history'],
    ['title' => 'My Profile', 'url' => '/my_profile.php', 'icon' => 'person'],
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
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
            <div class="flex items-center mb-12">
                <img alt="Site logo" class="h-8 mr-2" src="<?php echo $site_logo; ?>"/>
                <span class="text-2xl font-bold text-gray-800">User Panel</span>
            </div>
            <nav>
                <ul class="space-y-2">
                    <?php foreach ($nav_links as $link): ?>
                        <?php $is_active = is_active_link($link['url'], $current_uri); ?>
                        <li>
                            <a href="<?php echo $link['url']; ?>" class="flex items-center text-gray-600 hover:text-gray-900 font-medium p-2 rounded-lg transition-colors duration-200 <?php echo $is_active ? 'bg-gray-200 text-gray-900' : ''; ?>">
                                <span class="material-icons mr-3"><?php echo $link['icon']; ?></span>
                                <span><?php echo $link['title']; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
        <div>
            <nav>
                <ul>
                    <li class="mb-4">
                        <a class="flex items-center text-gray-600 hover:text-gray-900 font-medium text-sm p-2 rounded-lg transition-colors duration-200" href="#">
                            <span class="material-icons mr-3">help_outline</span>
                            Help &amp; information
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center text-gray-600 hover:text-gray-900 font-medium text-sm p-2 rounded-lg transition-colors duration-200" href="logout.php">
                            <span class="material-icons mr-3">logout</span>
                            Log out
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Overlay for mobile menu -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black opacity-0 md:hidden z-40 pointer-events-none transition-opacity duration-200 ease-in-out"></div>

    <main class="w-full md:flex-1 p-8 h-screen overflow-y-auto">

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