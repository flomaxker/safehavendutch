<?php
require_once __DIR__ . '/bootstrap.php';

use App\Models\Post;
use App\Models\Category;

if (!isset($_GET['slug'])) {
    header('Location: blog.php');
    exit;
}

$slug = $_GET['slug'];
$postModel = new Post($container->getPdo());
$post = $postModel->findBySlug($slug);

if (!$post) {
    http_response_code(404);
    // You can create a dedicated 404.php page and include it here
    echo "404 Not Found"; 
    exit;
}

// Fetch categories for this post
$categoryModel = new Category($container->getPdo());
$postCategories = $postModel->getPostCategories($post['id']);
$categories = [];
if (!empty($postCategories)) {
    // This could be more efficient, but it's fine for now.
    // A method like `getMultipleByIds` in the Category model would be better.
    foreach ($postCategories as $catId) {
        $categories[] = $categoryModel->findById($catId);
    }
}


$page_title = htmlspecialchars($post['title']);
include 'header.php';
?>

<div class="container mx-auto px-4 py-16">
    <article class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8 md:p-12">
        <header class="mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="text-gray-600">
                <span>Published on <?= date('F j, Y', strtotime($post['published_at'])) ?></span>
                <span class="mx-2">&bull;</span>
                <span>By <?= htmlspecialchars($post['author_name']) ?></span>
            </div>
            <?php if (!empty($categories)): ?>
                <div class="mt-4">
                    <?php foreach ($categories as $category): ?>
                        <?php if ($category): ?>
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">
                                <?= htmlspecialchars($category['name']) ?>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </header>
        
        <div class="prose prose-lg max-w-none text-gray-800">
            <?= $post['content'] // Content is already sanitized by TinyMCE, but you might want to add extra server-side sanitization here ?>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
