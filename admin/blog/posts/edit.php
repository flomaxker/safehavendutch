<?php
require_once __DIR__ . '/../../../bootstrap.php';

use App\Models\Post;
use App\Models\Category;
use App\Models\Setting;

$postModel = new Post($container->getPdo());
$categoryModel = new Category($container->getPdo());
$settingModel = new Setting($container->getPdo());

$tinymceApiKey = $settingModel->getSetting('tinymce_api_key');

$message = '';
$messageType = '';
$post = [
    'id' => null,
    'title' => '',
    'slug' => '',
    'content' => '',
    'status' => 'draft',
    'published_at' => null,
];
$postCategories = [];
$editing = false;

if (isset($_GET['id'])) {
    $post = $postModel->findById((int)$_GET['id']);
    $postCategories = $postModel->getPostCategories((int)$_GET['id']);
    $editing = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post['title'] = $_POST['title'] ?? '';
    $post['slug'] = $_POST['slug'] ?? '';
    $post['content'] = $_POST['content'] ?? '';
    $post['status'] = $_POST['status'] ?? 'draft';
    $post['published_at'] = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
    $selectedCategories = $_POST['categories'] ?? [];

    if ($editing) {
        $postId = (int)$_POST['id'];
        if ($postModel->update($postId, $post)) {
            $postModel->syncCategories($postId, $selectedCategories);
            $message = 'Post updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to update post.';
            $messageType = 'error';
        }
    } else {
        $post['user_id'] = $_SESSION['user_id']; // Assuming user ID is in session
        $postId = $postModel->create($post);
        if ($postId) {
            $postModel->syncCategories($postId, $selectedCategories);
            $message = 'Post created successfully!';
            $messageType = 'success';
            // Redirect to edit page to prevent re-submission
            header('Location: edit.php?id=' . $postId . '&status=created');
            exit;
        } else {
            $message = 'Failed to create post.';
            $messageType = 'error';
        }
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'created') {
    $message = 'Post created successfully!';
    $messageType = 'success';
}

$allCategories = $categoryModel->getAll('name', 'ASC');

$page_title = $editing ? 'Edit Post' : 'Create Post';
include __DIR__ . '/../../header.php';
?>

<div class="px-4 py-8 max-w-5xl mx-auto md:mx-0">

<?php
$apiKey = $tinymceApiKey ?? 'no-api-key';
?>
<script src="https://cdn.tiny.cloud/1/<?= $apiKey ?>/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
    });
</script>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800"><?= $page_title ?></h1>
    </div>
</header>

<?php if ($message): ?>
    <div class="mb-4 p-3 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="id" value="<?= $post['id'] ?>">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">Slug</label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content</label>
                    <textarea id="content" name="content" class="w-full"><?= htmlspecialchars($post['content']) ?></textarea>
                </div>
            </div>
        </div>
        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select id="status" name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= $post['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="published_at" class="block text-gray-700 text-sm font-bold mb-2">Published At</label>
                    <input type="datetime-local" id="published_at" name="published_at" value="<?= $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '' ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Categories</label>
                    <div class="h-48 overflow-y-auto border rounded p-2">
                        <?php foreach ($allCategories as $category): ?>
                            <div class="flex items-center">
                                <input type="checkbox" id="category_<?= $category['id'] ?>" name="categories[]" value="<?= $category['id'] ?>" <?= in_array($category['id'], $postCategories) ? 'checked' : '' ?> class="form-checkbox h-4 w-4 text-blue-600">
                                <label for="category_<?= $category['id'] ?>" class="ml-2 text-gray-700"><?= htmlspecialchars($category['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <?= $editing ? 'Update Post' : 'Create Post' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
