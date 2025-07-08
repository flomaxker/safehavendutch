<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (defined('USER_DASHBOARD_ACTIVE')) {
    return; // Stop execution if user dashboard is active
}

if (!headers_sent()) {
    $csp_policy = "default-src 'self'; ";
    $csp_policy .= "script-src 'self' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdn.tiny.cloud 'unsafe-eval'; ";
    $csp_policy .= "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; ";
    $csp_policy .= "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; ";
    $csp_policy .= "img-src 'self' data: https:; ";
    $csp_policy .= "connect-src 'self' https://*.tinymce.com https://sp.tinymce.com; ";
    $csp_policy .= "frame-src 'self' https://*.tinymce.com; ";
    $csp_policy .= "worker-src 'self' blob:;";
    header("Content-Security-Policy: " . $csp_policy);
}

use App\Models\Setting;
use App\Models\User;

$settingModel = new Setting($container->getPdo());
$settings = $settingModel->getAllSettings();
$siteLogo = $settings['site_logo'] ?? '/assets/images/default-logo.png';

$user_name = '';
if (isset($_SESSION['user_id'])) {
    $userModel = $container->getUserModel();
    $user = $userModel->find($_SESSION['user_id']);
    if ($user) {
        $user_name = $user['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO -->
    <title><?php echo htmlspecialchars($page['title'] ?? 'Safe Haven Dutch'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page['meta_description'] ?? 'Default meta description for Safe Haven Dutch.'); ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page['og_title'] ?? $page['title'] ?? 'Safe Haven Dutch'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page['og_description'] ?? $page['meta_description'] ?? 'Default OG description.'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($page['og_image'] ?? $siteLogo); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($page['og_url'] ?? (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
    <meta property="og:type" content="website">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/css/landing.css?v=<?php echo filemtime(__DIR__ . '/css/landing.css'); ?>">
    <link rel="stylesheet" href="/css/extras.css?v=<?php echo filemtime(__DIR__ . '/css/extras.css'); ?>">
    <link rel="stylesheet" href="/style.css?v=<?php echo filemtime(__DIR__ . '/style.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body>
<?php
if (!isset($nav_links)) {
    require_once __DIR__ . '/bootstrap.php';
}
?>
<div class="header-container">
    <nav class="navbar container flex items-center justify-between">
        <div class="flex items-center">
            <a href="index.php" class="logo-link mr-6">
                <img src="<?= htmlspecialchars($siteLogo) ?>" alt="Site Logo" class="logo-banner" style="height: 35px; width: auto;">
            </a>
            <ul class="nav-menu hidden md:flex items-center space-x-6" id="nav-menu-main">
                <?php foreach ($nav_links as $link): ?>
                    <li><a href="<?php echo htmlspecialchars($link['url']); ?>" class="nav-link text-gray-600 font-medium hover:text-primary-600"><?php echo htmlspecialchars($link['title']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="hidden md:flex items-center space-x-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $dashboard_url = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? '/admin/index.php' : '/dashboard.php';
                ?>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random&color=fff" alt="User Avatar" class="w-8 h-8 rounded-full">
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="<?= $dashboard_url ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="text-gray-600 font-medium hover:text-primary-600">Login</a>
                <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-full font-medium hover:bg-blue-700 transition">Register</a>
            <?php endif; ?>
        </div>

        <button class="nav-toggle md:hidden" id="nav-toggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
    </nav>
</div>

<div id="mobile-menu" class="hidden md:hidden fixed inset-0 bg-white z-50 p-6">
    <div class="flex justify-between items-center mb-8">
        <a href="index.php" class="logo-link">
            <img src="<?= htmlspecialchars($siteLogo) ?>" alt="Site Logo" class="logo-banner" style="height: 35px; width: auto;">
        </a>
        <button id="mobile-menu-close" aria-label="Close menu">
            <i class="fas fa-times text-2xl"></i>
        </button>
    </div>
    <ul class="space-y-4">
        <?php foreach ($nav_links as $link): ?>
            <li><a href="<?php echo htmlspecialchars($link['url']); ?>" class="block py-2 text-lg text-gray-700 font-semibold hover:text-primary-600"><?php echo htmlspecialchars($link['title']); ?></a></li>
        <?php endforeach; ?>
        <hr class="my-4">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            $dashboard_url = ($_SESSION['user_role'] === 'admin') ? '/admin/index.php' : '/dashboard.php';
            ?>
            <li><a href="<?= $dashboard_url ?>" class="block py-2 text-lg text-gray-700 font-semibold hover:text-primary-600">Dashboard</a></li>
            <li><a href="logout.php" class="block py-2 text-lg text-gray-700 font-semibold hover:text-primary-600">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="block py-2 text-lg text-gray-700 font-semibold hover:text-primary-600">Login</a></li>
            <li><a href="register.php" class="block py-2 text-lg text-gray-700 font-semibold hover:text-primary-600">Register</a></li>
        <?php endif; ?>
    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nav = document.querySelector('.navbar');
        const navToggle = document.getElementById('nav-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuClose = document.getElementById('mobile-menu-close');

        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        navToggle.addEventListener('click', function () {
            mobileMenu.classList.remove('hidden');
        });

        mobileMenuClose.addEventListener('click', function () {
            mobileMenu.classList.add('hidden');
        });
    });
</script>