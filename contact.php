<?php
require_once __DIR__ . '/bootstrap.php';

use App\Models\Page;

$pageModel = $container->getPageModel();
$page = $pageModel->findBySlug('contact');

$page_title = $page['title'] ?? 'Contact Us';
include 'header.php';
?>

<body class="bg-gray-50">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <!-- Contact Hero Section -->
    <section class="pt-24 bg-primary-100 relative">
        <div class="container mx-auto relative z-10 py-20 text-center">
            <h1 class="text-5xl font-bold text-primary-800 mb-4"><?php echo htmlspecialchars($page['title'] ?? 'Contact Us'); ?></h1>
            <p class="text-xl text-primary-700 max-w-3xl mx-auto">
                <?php echo htmlspecialchars($page['meta_description'] ?? "We'd love to hear from you!"); ?>
            </p>
        </div>
    </section>

    <section class="py-12">
        <div class="container mx-auto px-6">
            <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-lg">
                <?php echo nl2br(htmlspecialchars($page['main_content'] ?? 'Please use the form below to get in touch with us.')); ?>

                <form action="contact-handler.php" method="POST" class="mt-8 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" autocomplete="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" autocomplete="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" id="subject" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

</body>

<?php
include 'footer.php';
?>
</html>