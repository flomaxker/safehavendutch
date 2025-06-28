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
    ['title' => 'Pages', 'url' => '/admin/pages/index.php', 'icon' => 'description'],
    ['title' => 'Logout', 'url' => '/admin/logout.php', 'icon' => 'logout']
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
<body class="flex items-center justify-center min-h-screen p-4">
<div class="bg-white rounded-3xl shadow-2xl w-full max-w-7xl flex overflow-hidden" style="height: 90vh;">
<aside class="w-1/5 bg-white p-8 border-r border-gray-200 flex flex-col justify-between">
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
        if ($current_page === basename($link['url'])) {
            $is_active = true;
        } elseif ($current_dir === 'pages' && basename($link['url']) === 'index.php' && strpos($link['url'], 'pages') !== false) {
            $is_active = true;
        }
    ?>
    <li class="mb-6">
        <a class="flex items-center text-gray-600 hover:text-gray-900 font-medium <?php echo $is_active ? 'bg-gray-100 rounded-lg p-2 text-gray-900' : ''; ?>" href="<?php echo $link['url']; ?>">
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
<a class="flex items-center text-gray-600 hover:text-gray-900 font-medium text-sm" href="#">
<span class="material-icons mr-3">help_outline</span>
                                Help &amp; information
                            </a>
</li>
<li>
<a class="flex items-center text-gray-600 hover:text-gray-900 font-medium text-sm" href="logout.php">
<span class="material-icons mr-3">logout</span>
                                Log out
                            </a>
</li>
</ul>
</nav>
</div>
</aside>
<main class="w-4/5 bg-white p-8 overflow-y-auto">