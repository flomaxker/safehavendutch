<?php

// Explicitly load .env for CLI execution before any other includes
require_once __DIR__ . '/vendor/autoload.php'; // Ensure Composer autoloader is included

use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Now include bootstrap.php which relies on environment variables
require_once __DIR__ . '/bootstrap.php';

use App\Database\Database;
use App\Models\Page;

try {    $database = new Database();    $pdo = $database->getConnection();    echo "Successfully connected to the database!\n";    $pageModel = new Page($pdo); // Pass PDO to Page constructor

    $pages_to_create = [
        [
            'slug' => 'home',
            'page_type' => 'home',
            'title' => 'Welcome to Your Website',
            'meta_description' => 'Your platform for [Your Service/Content Type].',
            'og_title' => 'Your Website - Home',
            'og_description' => 'Explore our offerings and begin your experience.',
            'og_url' => '/',
            'og_image' => '/assets/images/default-hero.jpg',
            'hero_title' => 'Welcome to Your Platform',
            'hero_subtitle' => 'Your personalized solution for [Your Niche/Benefit].',
            'main_content' => 'This is the main content for the homepage. You can edit this text from the admin panel.',
            'features_heading' => 'Our Supportive Features',
            'feature1_icon' => 'school',
            'feature1_title' => 'Feature One Title',
            'feature1_description' => 'Description for feature one. Explain its benefits and what it offers.',
            'feature2_icon' => 'support_agent',
            'feature2_title' => 'Feature Two Title',
            'feature2_description' => 'Description for feature two. Highlight its unique aspects.',
            'feature3_icon' => 'groups',
            'feature3_title' => 'Feature Three Title',
            'feature3_description' => 'Description for feature three. Emphasize its value.',
        ],
        [
            'slug' => 'about',
            'page_type' => 'about',
            'title' => 'About Us',
            'meta_description' => 'Learn more about our CMS.',
            'og_title' => 'About Our CMS',
            'og_description' => 'Discover the mission and vision behind our Content Management System.',
            'og_url' => '/about.php',
            'og_image' => '/assets/images/default-blog-banner.jpg',
            'about_hero_title' => 'About Our Platform',
            'about_hero_subtitle' => 'Your trusted partner for [Your Niche/Service].',
            'about_mission_heading' => 'Our Mission',
            'about_mission_text' => 'Our mission is to empower individuals to [achieve a goal/solve a problem]. We provide [specific services/content], fostering [positive outcome].',
            'about_mission_image' => '/assets/images/Holistic.jpg',
            'about_founder_heading' => 'Meet the Founder',
            'about_founder_image' => '/assets/images/teacher-photo.jpg',
            'about_founder_name' => 'Our Founder',
            'about_founder_title' => 'Founder & [Your Role]',
            'about_founder_quote' => '"We believe in [core belief]. Our goal is to provide [benefit] where you can [positive outcome]."',
            'content' => 'This is the main content for the about page. You can edit this text from the admin panel.',
        ],
        [
            'slug' => 'contact',
            'title' => 'Contact Us',
            'meta_description' => 'Get in touch with us.',
            'og_title' => 'Contact Our CMS Support',
            'og_description' => 'Have questions or need support? Contact our team.',
            'og_url' => '/contact.php',
            'og_image' => '/assets/images/default-blog-banner.jpg',
            'content' => '<h2>Contact Us</h2><p>We\'d love to hear from you! Whether you have a question about our CMS, need technical support, or just want to provide feedback, feel free to reach out.</p>\n<p>You can reach us via email at <a href="mailto:info@yourcompany.com">info@yourcompany.com</a> or through the contact form on our homepage.</p>',
        ],
        [
            'slug' => 'privacy-policy',
            'title' => 'Privacy Policy',
            'meta_description' => 'Our privacy policy.',
            'og_title' => 'Privacy Policy',
            'og_description' => 'Understand how we collect, use, and protect your data.',
            'og_url' => '/privacy-policy.php',
            'og_image' => '/assets/images/default-blog-banner.jpg',
            'content' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy explains how we handle your personal information.</p>\n<p>We collect information to provide and improve our services. We do not share your personal information with third parties except as described in this policy or with your consent.</p>\n<p>For more details, please read our full privacy policy.</p>',
        ],
        [
            'slug' => 'terms',
            'title' => 'Terms of Service',
            'meta_description' => 'Our terms of service.',
            'og_title' => 'Terms of Service',
            'og_description' => 'Read the terms and conditions for using our CMS.',
            'og_url' => '/terms.php',
            'og_image' => '/assets/images/default-blog-banner.jpg',
            'content' => '<h2>Terms of Service</h2><p>Welcome to our CMS. By using our services, you agree to be bound by these Terms of Service.</p>\n<p>These terms govern your access to and use of our CMS, including all content, functionality, and services offered on or through the CMS.</p>\n<p>Please read these terms carefully before using our services.</p>',
        ],
        [
            'slug' => 'packages',
            'page_type' => 'packages',
            'title' => 'Our Packages',
            'meta_description' => 'Explore our coaching packages and pricing.',
            'og_title' => 'Our Packages - [Your Website Name]',
            'og_description' => 'Find the perfect coaching package to suit your needs.',
            'og_url' => '/packages.php',
            'og_image' => '/assets/images/default-blog-banner.jpg',
            'main_content' => 'This is the introductory content for the packages page. You can describe your different offerings here.',
        ],
    ];

    foreach ($pages_to_create as $page_data) {
        // Check if page already exists to prevent duplicates
        if (!$pageModel->findBySlug($page_data['slug'])) {
            $pageModel->create($page_data);
            echo "Page '" . $page_data['title'] . "' created successfully.\n";
        } else {
            echo "Page '" . $page_data['title'] . "' already exists. Skipping.\n";
        }
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}