<?php
$page_title = "Manage Pages";
include __DIR__ . '/../header.php';

use App\Models\Page;

$pageModel = $container->getPageModel();
$pages = $pageModel->getAll();

?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Pages</h1>
        <p class="text-gray-500">View and edit your website pages.</p>
    </div>
</header>

<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    ID
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Title
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Slug
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Page Type
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pages)): ?>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($page['id']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($page['title']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($page['slug']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($page['page_type'] ?? 'standard'); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="edit.php?id=<?php echo $page['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        No pages found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include __DIR__ . '/../footer.php';
?>