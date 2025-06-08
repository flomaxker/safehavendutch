<?php
// Load Composer autoloader if present, otherwise fallback to a simple autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        $path = __DIR__ . '/app/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    });
}

use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    Dotenv::createUnsafeImmutable(__DIR__)->safeLoad();
}

$container = new Container();
