<?php
require_once __DIR__ . '/bootstrap.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Admin specific site settings
$site_name = "Admin Panel";
$site_logo = "/assets/images/default-logo.png"; // Generic logo for admin
$nav_links = [
    ['title' => 'Dashboard', 'url' => '/admin/index.php', 'icon' => 'home'],
    ['title' => 'Users', 'url' => '/admin/users/index.php', 'icon' => 'group'],
    ['title' => 'Children', 'url' => '/admin/children/index.php', 'icon' => 'child_care'],
    ['title' => 'Packages', 'url' => '/admin/packages/index.php', 'icon' => 'redeem'],
    ['title' => 'Bookings', 'url' => '/admin/bookings/index.php', 'icon' => 'event_note'],
    ['title' => 'Blog Posts', 'url' => '/admin/blog/posts/index.php', 'icon' => 'article'],
    ['title' => 'Blog Categories', 'url' => '/admin/blog/categories/index.php', 'icon' => 'label'],
    ['title' => 'Email Templates', 'url' => '/admin/emails/index.php', 'icon' => 'email'],
    ['title' => 'System Settings', 'url' => '/admin/settings/index.php', 'icon' => 'settings_applications'],
    ['title' => 'File Management', 'url' => '/admin/files/index.php', 'icon' => 'folder'],
    ['title' => 'GDPR Tools', 'url' => '/admin/gdpr/index.php', 'icon' => 'privacy_tip'],
    ['title' => 'Reports', 'url' => '/admin/reports/index.php', 'icon' => 'bar_chart'],
    ['title' => 'Teachers', 'url' => '/admin/teachers/index.php', 'icon' => 'person'],
    ['title' => 'Rate Limiting', 'url' => '/admin/rate_limiting/index.php', 'icon' => 'speed']
];

// Determine active link for styling
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo $page_title ?? 'Admin'; ?> | <?php echo $site_name; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
                <span class="text-2xl font-bold text-gray-800">Admin Panel</span>
            </div>
            <nav>
                <ul>
                    <?php foreach ($nav_links as $link): ?>
                        <?php
                            $is_active = false;
                            $is_active = (strpos($_SERVER['REQUEST_URI'], $link['url']) === 0);
                        ?>
                        <li class="mb-6">
        <a class="flex items-center text-gray-600 hover:text-gray-900 font-medium p-2 rounded-lg transition-colors duration-200 <?php echo $is_active ? 'bg-gray-200 text-gray-900' : ''; ?>" href="<?php echo $link['url']; ?>">
            <span class="material-icons mr-3"><?php echo $link['icon']; ?></span>
            <?php echo $link['title']; ?>
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