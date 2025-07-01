<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;

$pdo = (new Database())->getConnection();

$sql = file_get_contents(__DIR__ . '/../migrations/002_packages_and_purchases.sql');

$pdo->exec($sql);

echo "Migration 002 executed successfully.";
