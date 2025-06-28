<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Haven Dutch</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/css/extras.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<?php

// If not, you might need to include bootstrap.php here or pass $nav_links to this file.
// If not, you might need to include bootstrap.php here or pass $nav_links to this file.

// Example of how $nav_links might look (defined in bootstrap.php)
/*
$nav_links = [
    ['title' => 'Home', 'url' => 'index.php'],
    ['title' => 'About', 'url' => 'about.php'],
    ['title' => 'Contact', 'url' => 'contact.php'],
    // ... other static links
];
*/

// Ensure $nav_links is available, if not, define a default or include bootstrap
if (!isset($nav_links)) {
    require_once __DIR__ . '/bootstrap.php';
}

?>
<nav class="navbar container">
    <a href="index.php" class="logo-link">
        <img src="/assets/images/default-logo.png" alt="CMS Logo" class="logo-banner" style="height: 40px;">
    </a>
    <ul class="nav-menu" id="nav-menu">
        <?php foreach ($nav_links as $link): ?>
            <li><a href="<?php echo htmlspecialchars($link['url']); ?>" class="nav-link"><?php echo htmlspecialchars($link['title']); ?></a></li>
        <?php endforeach; ?>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id']): // Check for user_id instead of user_logged_in ?>
            <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="packages.php" class="nav-link">Packages</a></li>
            <li><a href="purchase_history.php" class="nav-link">History</a></li>
            <li><a href="logout.php" class="nav-link">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="nav-link">Login</a></li>
            <li><a href="admin/login.php" class="nav-link">Admin Login</a></li>
            <li><a href="register.php" class="nav-link">Register</a></li>
        <?php endif; ?>
    </ul>
    <button class="nav-toggle" id="nav-toggle" aria-label="Menu">
        <i class="fas fa-bars"></i>
    </button>
</nav>
