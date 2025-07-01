<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!empty($name)) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}

$container = new Container();

// Define global navigation links
$nav_links = [
    ['title' => 'Home', 'url' => 'index.php'],
    ['title' => 'About', 'url' => 'about.php'],
    ['title' => 'Packages', 'url' => 'packages.php'],
    ['title' => 'Blog', 'url' => 'blog.php'],
    ['title' => 'Contact', 'url' => 'contact.php']
];