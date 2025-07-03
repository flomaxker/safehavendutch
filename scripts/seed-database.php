<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Models\User;
use App\Models\Package;
use App\Models\Child;

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
            $existingUser['role'], // Keep existing role
            $existingUser['quick_actions_order'] ?? '[]' // Pass existing quick_actions_order or empty array
        );
        $createdUserIds[] = $existingUser['id']; // Add existing user ID to the list for package assignment
        echo 'User updated: ' . $userData['name'] . ' (ID: ' . $existingUser['id'] . ')' . PHP_EOL;
    } else {
        // User does not exist, create new user
        $userId = $userModel->create($userData['name'], $userData['email'], password_hash($userData['password'], PASSWORD_DEFAULT), 'member', 0);
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


// Seed Children
$childModel = new App\Models\Child($pdo);

$fakeChildren = [
    ['name' => 'Max', 'date_of_birth' => '2015-03-10', 'notes' => 'Loves drawing.'],
    ['name' => 'Sophie', 'date_of_birth' => '2017-07-22', 'notes' => 'Enjoys reading.'],
    ['name' => 'Liam', 'date_of_birth' => '2016-01-05', 'notes' => 'Very energetic.'],
    ['name' => 'Olivia', 'date_of_birth' => '2018-11-30', 'notes' => 'Quiet and observant.'],
    ['name' => 'Noah', 'date_of_birth' => '2014-09-18', 'notes' => 'Plays soccer.'],
    ['name' => 'Emma', 'date_of_birth' => '2019-04-01', 'notes' => 'Always smiling.'],
    ['name' => 'Lucas', 'date_of_birth' => '2013-06-25', 'notes' => 'Good at math.'],
    ['name' => 'Ava', 'date_of_birth' => '2017-02-14', 'notes' => 'Creative and artistic.'],
    ['name' => 'Elijah', 'date_of_birth' => '2015-10-03', 'notes' => 'Loves animals.'],
    ['name' => 'Mia', 'date_of_birth' => '2018-08-08', 'notes' => 'Curious and adventurous.'],
];

$allUserIds = $userModel->getAllUserIds(); // Assuming a method to get all user IDs

if (!empty($allUserIds)) {
    foreach ($fakeChildren as $childData) {
        // Assign a random user_id to each child
        $randomUserId = $allUserIds[array_rand($allUserIds)];
        $childData['user_id'] = $randomUserId;

        // Check if child already exists for this user to prevent duplicates on re-seeding
        // This is a simple check, more robust would be to check name + user_id
        $existingChild = null; // You might need to add a findByNameAndUserId method to ChildModel
        // For now, we'll just create them. If re-seeding is frequent, consider a more robust check.

        if ($childModel->create($childData)) {
            echo 'Child created: ' . $childData['name'] . ' for user ID: ' . $childData['user_id'] . PHP_EOL;
        } else {
            echo 'Failed to create child: ' . $childData['name'] . PHP_EOL;
        }
    }
} else {
    echo 'No users found to associate children with.' . PHP_EOL;
}

echo 'Database seeding complete.' . PHP_EOL;

