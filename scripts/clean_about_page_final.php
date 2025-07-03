<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Page;

$pageModel = $container->getPageModel();
$page_id = 2; // The ID of the "about" page

// Fetch the current, messy content
$page = $pageModel->findById($page_id);
$messy_content = $page['main_content'];

// A simple regex to strip all class attributes from any HTML tag
$clean_content = preg_replace('/\\s+class="[^"]*"/i', '', $messy_content);

// Update the database with the clean content
$pageModel->update($page_id, [
    "main_content" => $clean_content,
    "title" => "About Us",
    "slug" => "about",
    "hero_title" => "About Safe Haven Dutch",
    "hero_subtitle" => "We're dedicated to helping you feel at home in the Netherlands through language, culture, and community."
]);

echo "Successfully stripped all CSS classes from the About page content.\n";

