<?php
require_once __DIR__ . '/bootstrap.php';

$pdo = $container->getPdo();
$stmt = $pdo->query('DESCRIBE packages');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
