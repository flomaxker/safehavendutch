<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\User;

$db = new Database();
$pdo = $db->getConnection();
$userModel = new User($pdo);

$teachers = $userModel->getTeachers();

$page_title = "Teachers Management";
include __DIR__ . '/../header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-4">Teachers Management</h1>
<p class="text-gray-500">This page allows you to manage teacher profiles and their iCal availability URLs.</p>

<div class="overflow-x-auto relative shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="py-3 px-6">ID</th>
                <th scope="col" class="py-3 px-6">Name</th>
                <th scope="col" class="py-3 px-6">Email</th>
                <th scope="col" class="py-3 px-6">iCal URL</th>
                <th scope="col" class="py-3 px-6">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="py-4 px-6"><?= htmlspecialchars($teacher['id']) ?></td>
                    <td class="py-4 px-6"><?= htmlspecialchars($teacher['name']) ?></td>
                    <td class="py-4 px-6"><?= htmlspecialchars($teacher['email']) ?></td>
                    <td class="py-4 px-6">
                        <?php if (!empty($teacher['ical_url'])): ?>
                            <a href="<?= htmlspecialchars($teacher['ical_url']) ?>" target="_blank" class="text-blue-600 hover:underline">Link</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-6">
                        <a href="../users/edit.php?id=<?= htmlspecialchars($teacher['id']) ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../footer.php'; ?>