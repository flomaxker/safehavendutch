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
            'slug' => 'about',
            'title' => 'About Us',
            'meta_description' => 'Learn more about our CMS.',
            'og_title' => 'About Our CMS',
            'og_description' => 'Discover the mission and vision behind our Content Management System.',
            'og_url' => '/about.php',
            'og_image' => '/assets/images/default-blog-banner.jpg',
            'content' => '<h2>About Our CMS</h2><p>This is a versatile Content Management System designed to empower individuals and organizations to easily manage their web content, users, and services. Our goal is to provide a flexible and intuitive platform that adapts to various needs, from educational institutions to community groups and small businesses.</p>\n<p>We believe in simplicity, efficiency, and providing the tools necessary for you to focus on what matters most: your content and your community.</p>',
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