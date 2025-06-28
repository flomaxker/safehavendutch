<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Models\User;
use App\Models\Package;

try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    echo 'Database connection failed: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Seed Users
$userModel = new User($pdo);

// Check if admin user already exists
$adminUser = $userModel->findByUsername('admin');
if (!$adminUser) {
    $adminUserId = $userModel->create('admin', 'admin@example.com', password_hash('password', PASSWORD_DEFAULT), 'admin');
    echo 'Admin user created with ID: ' . $adminUserId . PHP_EOL;
} else {
    echo 'Admin user already exists.' . PHP_EOL;
}

// Seed Packages
$packageModel = new Package($pdo);

$packagesToSeed = [
    [
        'name' => 'Basic Package',
        'description' => 'A basic package with 10 credits.',
        'price_cents' => 1000, // €10.00
        'credit_amount' => 10
    ],
    [
        'name' => 'Standard Package',
        'description' => 'A standard package with 25 credits.',
        'price_cents' => 2000, // €20.00
        'credit_amount' => 25
    ],
    [
        'name' => 'Premium Package',
        'description' => 'A premium package with 50 credits.',
        'price_cents' => 4000, // €40.00
        'credit_amount' => 50
    ],
];

foreach ($packagesToSeed as $packageData) {
    $existingPackage = $packageModel->findByName($packageData['name']);
    if (!$existingPackage) {
        $packageId = $packageModel->create(
            $packageData['name'],
            $packageData['description'],
            $packageData['price_cents'],
            $packageData['credit_amount']
        );
        echo 'Package created: ' . $packageData['name'] . ' with ID: ' . $packageId . PHP_EOL;
    } else {
        echo 'Package already exists: ' . $packageData['name'] . PHP_EOL;
    }
}

echo 'Database seeding complete.' . PHP_EOL;
