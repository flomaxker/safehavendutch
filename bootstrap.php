<?php
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
    Dotenv::createUnsafeImmutable(__DIR__)->safeLoad();
}

$container = new Container();
