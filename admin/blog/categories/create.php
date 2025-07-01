<?php
require_once __DIR__ . '/../../../bootstrap.php';

use App\Models\Category;

$categoryModel = new Category($container->getPdo());

$name = '';
$slug = '';
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $slug = $_POST['slug'] ?? '';

    if (empty($name) || empty($slug)) {
        $message = 'Please fill in all fields.';
        $messageType = 'error';
    } else {
        if ($categoryModel->create(['name' => $name, 'slug' => $slug])) {
            header('Location: index.php?message=Category+created+successfully&type=success');
            exit;
        } else {
            $message = 'Failed to create category. The slug may already exist.';
            $messageType = 'error';
        }
    }
}

$page_title = 'Create Category';
include __DIR__ . '/../../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Create New Category</h1>
        <p class="text-gray-500">Fill out the form below to add a new category.</p>
    </div>
    <a href="index.php" class="text-blue-600 hover:underline">Back to Categories</a>
</header>

<?php if ($message): ?>
    <div class="mb-4 p-3 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="bg-white p-8 rounded-lg shadow-md">
    <form method="post">
        <div class="mb-6">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Category Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="mb-6">
            <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">Slug</label>
            <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <p class="text-gray-600 text-xs italic mt-2">The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</p>
        </div>
        <div class="flex items-center">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Category
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
