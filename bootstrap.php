<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('PROJECT_ROOT', __DIR__);

// Load Composer autoloader if present, otherwise fallback to a simple autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $path = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($path)) {
                require_once $path;
            }
        }
    });
}

use Dotenv\Dotenv;
use App\Container;

// Generate a nonce for CSP
$nonce = bin2hex(random_bytes(16));

// Load environment variables from .env file
if (file_exists(PROJECT_ROOT . '/.env')) {
    $dotenv = Dotenv::createUnsafeImmutable(PROJECT_ROOT);
    $dotenv->load();

    // Define mail constants from environment variables
    define('MAIL_HOST', getenv('MAIL_HOST'));
    define('MAIL_USERNAME', getenv('MAIL_USERNAME'));
    define('MAIL_PASSWORD', getenv('MAIL_PASSWORD'));
    define('MAIL_PORT', getenv('MAIL_PORT'));
    define('MAIL_FROM_ADDRESS', getenv('MAIL_FROM_ADDRESS'));
    define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME'));
}

$container = new Container();

// Define global navigation links
$nav_links = [
    ['title' => 'Home', 'url' => 'index.php'],
    ['title' => 'About', 'url' => 'about.php'],
    ['title' => 'Packages', 'url' => 'packages.php'],
    ['title' => 'Blog', 'url' => 'blog.php'],
    ['title' => 'Contact', 'url' => 'page.php?slug=contact']
];