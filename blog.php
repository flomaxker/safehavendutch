<?php
require_once __DIR__ . '/bootstrap.php';

use App\Models\Post;

$postModel = new Post($container->getPdo());
$posts = $postModel->getPublishedPosts();

$page_title = 'Blog';
include 'header.php';
?>

<body class="bg-gray-50">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <!-- Blog Hero Section -->
    <section class="pt-24 bg-primary-100 relative">
        <div class="container mx-auto relative z-10 py-20 text-center">
            <h1 class="text-5xl font-bold text-primary-800 mb-4">Our Blog</h1>
            <p class="text-xl text-primary-700 max-w-3xl mx-auto">
                Insights, stories, and advice on navigating life abroad.
            </p>
        </div>
    </section>

    <!-- Blog Grid Section -->
    <section class="py-20">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col">
                            <div class="p-6 flex-grow">
                                <p class="text-sm text-gray-500 mb-2">
                                    Published on <?= date('F j, Y', strtotime($post['published_at'])) ?>
                                </p>
                                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                                    <a href="post.php?slug=<?= htmlspecialchars($post['slug']) ?>" class="hover:text-primary-600">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </a>
                                </h2>
                                <div class="text-gray-600">
                                    <?php
                                    $excerpt = strip_tags($post['content']);
                                    if (strlen($excerpt) > 120) {
                                        $excerpt = substr($excerpt, 0, 120) . '...';
                                    }
                                    echo $excerpt;
                                    ?>
                                </div>
                            </div>
                            <div class="p-6 bg-gray-50">
                                <a href="post.php?slug=<?= htmlspecialchars($post['slug']) ?>" class="font-bold text-primary-600 hover:text-primary-700">
                                    Read More &rarr;
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center text-gray-600">
                        <p>No blog posts have been published yet. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

</body>

<?php
include 'footer.php';
?>
</html>