<?php
require_once __DIR__ . '/bootstrap.php';
use App\Models\Page;

$pageModel = new Page();
$page = $pageModel->findBySlug('about');

if ($page) {
    $page_title = $page['title'];
    $meta_description = $page['meta_description'];
    $canonical_url = $page['og_url'];
    $og_title = $page['og_title'];
    $og_description = $page['og_description'];
    $og_url = $page['og_url'];
    $og_image = $page['og_image'];
    $page_content = $page['content'];
} else {
    // Fallback or error handling if page not found
    $page_title = "Page Not Found";
    $meta_description = "The requested page could not be found.";
    $canonical_url = "/";
    $og_title = "Page Not Found";
    $og_description = "The requested page could not be found.";
    $og_url = "/";
    $og_image = "/assets/images/default-blog-banner.jpg";
    $page_content = "<div class=\"container\"><h1>Page Not Found</h1><p>Sorry, the page you are looking for does not exist.</p></div>";
}



// Set the path to the theme directory
$theme_path = __DIR__ . '/templates/theme/';

// Load the page template
include $theme_path . 'page.php';
?>