<?php
$page_title = "My Profile";
require_once __DIR__ . '/user_header.php';

$userModel = $container->getUserModel();

// Get the user's role
$loggedInUser = $userModel->find($_SESSION['user_id']);
if ($loggedInUser && $loggedInUser['role'] === 'admin') {
    header('Location: /admin/index.php');
    exit();
}
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Profile</h1>
    <p class="text-gray-600">This page will allow you to manage your profile information.</p>
</div>

