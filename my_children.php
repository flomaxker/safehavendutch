<?php
$page_title = "My Children";
require_once __DIR__ . '/user_header.php';

use App\Models\Child;

$userId = $_SESSION['user_id'];
$childModel = $container->getChildModel();

$children = $childModel->findByUserId($userId);

?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Children</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['success_message']); ?></span>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['error_message']); ?></span>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    

    <!-- Existing Children List -->
    
    <?php if (empty($children)): ?>
        <p class="text-gray-600">You have no children added yet.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($children as $child): ?>
                <div class="bg-gray-50 p-6 rounded-2xl shadow-md flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($child['name']); ?></h3>
                        <p class="text-gray-600 text-sm mb-1"><strong>Date of Birth:</strong> <?php echo htmlspecialchars($child['date_of_birth']); ?></p>
                        <p class="text-gray-600 text-sm"><strong>Notes:</strong> <?php echo htmlspecialchars($child['notes']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

