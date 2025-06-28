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

$fakeUsers = [
    ['name' => 'The Jansen Family', 'email' => 'jansen@example.com', 'password' => 'password'],
    ['name' => 'The Schmidt Family', 'email' => 'schmidt@example.com', 'password' => 'password'],
    ['name' => 'The Rossi Family', 'email' => 'rossi@example.com', 'password' => 'password'],
    ['name' => 'The Kim Family', 'email' => 'kim@example.com', 'password' => 'password'],
    ['name' => 'The Dubois Family', 'email' => 'dubois@example.com', 'password' => 'password'],
    ['name' => 'The Silva Family', 'email' => 'silva@example.com', 'password' => 'password'],
    ['name' => 'The Müller Family', 'email' => 'muller@example.com', 'password' => 'password'],
    ['name' => 'The Chen Family', 'email' => 'chen@example.com', 'password' => 'password'],
    ['name' => 'The Garcia Family', 'email' => 'garcia@example.com', 'password' => 'password'],
    ['name' => 'The Smith Family', 'email' => 'smith@example.com', 'password' => 'password'],
];

$createdUserIds = [];
foreach ($fakeUsers as $userData) {
    $existingUser = $userModel->findByEmail($userData['email']);
    if (!$existingUser) {
        $userId = $userModel->create($userData['name'], $userData['email'], password_hash($userData['password'], PASSWORD_DEFAULT), 'student');
        $createdUserIds[] = $userId;
        echo 'User created: ' . $userData['name'] . ' with ID: ' . $userId . PHP_EOL;
    } else {
        echo 'User already exists: ' . $userData['name'] . PHP_EOL;
    }
}

// Seed Packages
$packageModel = new Package($pdo);

$packagesToSeed = [
    [
        'name' => 'Basic Package',
        'description' => 'A basic package with 10 credits.',
        'price_cents' => 1000, // €10.00
        'credit_amount' => 10,
        'active' => true
    ],
    [
        'name' => 'Standard Package',
        'description' => 'A standard package with 25 credits.',
        'price_cents' => 2000, // €20.00
        'credit_amount' => 25,
        'active' => true
    ],
    [
        'name' => 'Premium Package',
        'description' => 'A premium package with 50 credits.',
        'price_cents' => 4000, // €40.00
        'credit_amount' => 50,
        'active' => true
    ],
];

$createdPackages = [];
foreach ($packagesToSeed as $packageData) {
    $existingPackage = $packageModel->findByName($packageData['name']);
    if (!$existingPackage) {
        $packageId = $packageModel->create(
            $packageData['name'],
            $packageData['description'],
            $packageData['credit_amount'],
            $packageData['price_cents'],
            $packageData['active']
        );
        $createdPackages[] = $packageModel->getById($packageId);
        echo 'Package created: ' . $packageData['name'] . ' with ID: ' . $packageId . PHP_EOL;
    } else {
        $createdPackages[] = $existingPackage;
        echo 'Package already exists: ' . $packageData['name'] . PHP_EOL;
    }
}

// Assign packages to half the families
if (!empty($createdUserIds) && !empty($createdPackages)) {
    $packageToAssign = $createdPackages[array_rand($createdPackages)]; // Pick a random package
    $usersToAssignCount = ceil(count($createdUserIds) / 2);
    shuffle($createdUserIds); // Randomize user order

    for ($i = 0; $i < $usersToAssignCount; $i++) {
        $userId = $createdUserIds[$i];
        if ($userModel->updateCreditBalance($userId, $packageToAssign['credit_amount'])) {
            echo 'Assigned ' . $packageToAssign['credit_amount'] . ' credits to user ID: ' . $userId . ' (' . $packageToAssign['name'] . ')' . PHP_EOL;
        } else {
            echo 'Failed to assign credits to user ID: ' . $userId . PHP_EOL;
        }
    }
} else {
    echo 'No users or packages to assign.' . PHP_EOL;
}

echo 'Database seeding complete.' . PHP_EOL;
