<?php
require_once __DIR__ . '/../../../bootstrap.php';

use App\Models\Category;

$categoryModel = new Category($container->getPdo());

$message = '';
$messageType = '';

// Handle messages from redirects
if (isset($_GET['message'], $_GET['type'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = htmlspecialchars($_GET['type']);
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    if ($categoryModel->delete((int)$_POST['id'])) {
        $message = 'Category deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Failed to delete category.';
        $messageType = 'error';
    }
}

$categories = $categoryModel->getAll('name', 'ASC');

$page_title = 'Manage Categories';
include __DIR__ . '/../../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Categories</h1>
        <p class="text-gray-500">A list of all blog post categories.</p>
    </div>
    <a href="create.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        <i class="fas fa-plus mr-2"></i>Add New Category
    </a>
</header>

<?php if ($message): ?>
    <div class="mb-4 p-3 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Slug</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($category['name']) ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($category['slug']) ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="edit.php?id=<?= $category['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-pencil-alt mr-1"></i>Edit
                            </a>
                            <form method="post" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                <button type="submit" name="action" value="delete" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                    No categories found. <a href="create.php" class="text-blue-500 hover:underline">Create one now</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>