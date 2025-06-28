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
    $adminUserId = $userModel->create('admin', 'admin@example.com', password_hash('password', PASSWORD_DEFAULT), 'admin', 0);
    echo 'Admin user created with ID: ' . $adminUserId . PHP_EOL;
} else {
    echo 'Admin user already exists.' . PHP_EOL;
}

$fakeUsers = [
    ['name' => 'Anna Jansen', 'email' => 'anna.jansen@example.com', 'password' => 'password'],
    ['name' => 'Mark Schmidt', 'email' => 'mark.schmidt@example.com', 'password' => 'password'],
    ['name' => 'Sofia Rossi', 'email' => 'sofia.rossi@example.com', 'password' => 'password'],
    ['name' => 'Joon Kim', 'email' => 'joon.kim@example.com', 'password' => 'password'],
    ['name' => 'Pierre Dubois', 'email' => 'pierre.dubois@example.com', 'password' => 'password'],
    ['name' => 'Maria Silva', 'email' => 'maria.silva@example.com', 'password' => 'password'],
    ['name' => 'Lukas Müller', 'email' => 'lukas.muller@example.com', 'password' => 'password'],
    ['name' => 'Mei Chen', 'email' => 'mei.chen@example.com', 'password' => 'password'],
    ['name' => 'Carlos Garcia', 'email' => 'carlos.garcia@example.com', 'password' => 'password'],
    ['name' => 'Emily Smith', 'email' => 'emily.smith@example.com', 'password' => 'password'],
];

$createdUserIds = [];
foreach ($fakeUsers as $userData) {
    $existingUser = $userModel->findByEmail($userData['email']);
    if ($existingUser) {
        // User exists, update their name and ensure role/credits are consistent
        $userModel->update(
            $existingUser['id'],
            $userData['name'],
            $userData['email'],
            $existingUser['euro_balance'], // Keep existing euro balance
            $existingUser['role'] // Keep existing role
        );
        $createdUserIds[] = $existingUser['id']; // Add existing user ID to the list for package assignment
        echo 'User updated: ' . $userData['name'] . ' (ID: ' . $existingUser['id'] . ')' . PHP_EOL;
    } else {
        // User does not exist, create new user
        $userId = $userModel->create($userData['name'], $userData['email'], password_hash($userData['password'], PASSWORD_DEFAULT), 'student', 0);
        $createdUserIds[] = $userId;
        echo 'User created: ' . $userData['name'] . ' with ID: ' . $userId . PHP_EOL;
    }
}

// Seed Packages
$packageModel = new Package($pdo);

$packagesToSeed = [
    [
        'name' => 'Basic Package',
        'description' => 'A basic package with 10 Euros.',
        'price_cents' => 1000, // €10.00
        'euro_value' => 10,
        'active' => true
    ],
    [
        'name' => 'Standard Package',
        'description' => 'A standard package with 25 Euros.',
        'price_cents' => 2000, // €20.00
        'euro_value' => 25,
        'active' => true
    ],
    [
        'name' => 'Premium Package',
        'description' => 'A premium package with 50 Euros.',
        'price_cents' => 4000, // €40.00
        'euro_value' => 50,
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
            $packageData['euro_value'],
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
        if ($userModel->updateEuroBalance($userId, $packageToAssign['euro_value'])) {
            echo 'Assigned ' . $packageToAssign['euro_value'] . ' Euros to user ID: ' . $userId . ' (' . $packageToAssign['name'] . ')' . PHP_EOL;
        } else {
            echo 'Failed to assign Euros to user ID: ' . $userId . PHP_EOL;
        }
    }
} else {
    echo 'No users or packages to assign.' . PHP_EOL;
}

echo 'Database seeding complete.' . PHP_EOL;
