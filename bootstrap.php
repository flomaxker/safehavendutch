<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    Dotenv::createUnsafeImmutable(__DIR__)->safeLoad();
}
