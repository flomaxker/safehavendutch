
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo $canonical_url; ?>">
    <meta property="og:title" content="<?php echo $og_title; ?>">
    <meta property="og:description" content="<?php echo $og_description; ?>">
    <meta property="og:url" content="<?php echo $og_url; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo $og_image; ?>">
    <meta name="twitter:card" content="summary_large_image">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar container">
            <a href="/index.php" class="logo-link">
                <img loading="lazy" src="<?php echo $site_logo; ?>" alt="<?php echo $site_name; ?> Logo" class="logo-banner">
            </a>
            <ul class="nav-menu" id="nav-menu">
                <?php foreach ($nav_links as $link): ?>
                    <li><a href="<?php echo $link['url']; ?>" class="nav-link"><?php echo $link['title']; ?></a></li>
                <?php endforeach; ?>
            </ul>
            <button class="nav-toggle" id="nav-toggle" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
        <div id="scroll-progress" class="scroll-progress-bar"></div>
    </header>
    <main>
