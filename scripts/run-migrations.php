<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    fwrite(STDERR, 'Connection failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

$migrations = glob(__DIR__ . '/../migrations/*.sql');
if (!$migrations) {
    echo 'No migrations found.' . PHP_EOL;
    exit(0);
}

sort($migrations);

foreach ($migrations as $file) {
    echo 'Running migration: ' . basename($file) . PHP_EOL;
    $sql = file_get_contents($file);
    if ($sql === false) {
        fwrite(STDERR, 'Failed to read migration file: ' . $file . PHP_EOL);
        exit(1);
    }
    try {
        $pdo->exec($sql);
        echo 'Applied: ' . basename($file) . PHP_EOL;
    } catch (PDOException $e) {
        fwrite(STDERR, 'Error applying ' . basename($file) . ': ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}

echo 'All migrations have been applied.' . PHP_EOL;