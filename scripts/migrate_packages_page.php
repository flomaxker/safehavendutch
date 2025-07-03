<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Page;

$pageModel = $container->getPageModel();
$page_id = 6; // The ID of the "packages" page

$hero_title = "Our Packages";
$hero_subtitle = "Choose the perfect plan to start your journey with us. All packages include personalized coaching and access to our community.";

$pageModel->update($page_id, [
    "hero_title" => $hero_title,
    "hero_subtitle" => $hero_subtitle,
    "show_packages" => 1,
    "title" => "Our Packages",
    "slug" => "packages"
]);

echo "Packages page migrated successfully.\n";