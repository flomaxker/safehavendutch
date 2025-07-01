<?php
require_once __DIR__ . '/../../../bootstrap.php';

use App\Models\Post;

$postModel = new Post($container->getPdo());

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    if ($postModel->delete((int)$_POST['id'])) {
        $message = 'Post deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Failed to delete post.';
        $messageType = 'error';
    }
}

$posts = $postModel->getAll('created_at', 'DESC');

$page_title = 'Manage Blog Posts';
include __DIR__ . '/../../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Blog Posts</h1>
        <p class="text-gray-500">Create, edit, and delete blog posts.</p>
    </div>
    <a href="edit.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        Create New Post
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
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Author</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Published At</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($post['title']) ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($post['author_name']) ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($post['status']) ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= $post['published_at'] ? date('M d, Y H:i', strtotime($post['published_at'])) : 'N/A' ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="edit.php?id=<?= $post['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form method="post" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                <button type="submit" name="action" value="delete" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No posts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
