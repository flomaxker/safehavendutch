<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    fwrite(STDERR, 'Connection failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

try {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )'
    );
} catch (PDOException $e) {
    fwrite(STDERR, 'Failed to ensure migrations table: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

$migrations = glob(__DIR__ . '/../migrations/*.sql');
if (!$migrations) {
    echo 'No migrations found.' . PHP_EOL;
    exit(0);
}

sort($migrations);

foreach ($migrations as $file) {
    $filename = basename($file);
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM migrations WHERE filename = :filename');
    $stmt->execute(['filename' => $filename]);
    if ($stmt->fetchColumn() > 0) {
        echo 'Skipping already applied migration: ' . $filename . PHP_EOL;
        continue;
    }

    echo 'Running migration: ' . $filename . PHP_EOL;
    $sql = file_get_contents($file);
    if ($sql === false) {
        fwrite(STDERR, 'Failed to read migration file: ' . $file . PHP_EOL);
        exit(1);
    }

    try {
        $pdo->exec($sql);
        $insert = $pdo->prepare('INSERT INTO migrations (filename) VALUES (:filename)');
        $insert->execute(['filename' => $filename]);
        echo 'Applied: ' . $filename . PHP_EOL;
    } catch (PDOException $e) {
        $errorInfo = $e->errorInfo;
        if (isset($errorInfo[1]) && (int)$errorInfo[1] === 1050) {
            echo 'Skipping migration (table already exists): ' . $filename . PHP_EOL;
            $insert = $pdo->prepare('INSERT INTO migrations (filename) VALUES (:filename)');
            $insert->execute(['filename' => $filename]);
            continue;
        }
        fwrite(STDERR, 'Error applying ' . $filename . ': ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}

echo 'All migrations have been applied.' . PHP_EOL;