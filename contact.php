<?php
require_once __DIR__ . '/bootstrap.php';
use App\Models\Page;

$pageModel = $container->getPageModel();
$page = $pageModel->findBySlug('contact');

$page_title = 'Contact Us';
if ($page) {
    $page_title = $page['title'];
}

include 'header.php';
?>

<body class="bg-gray-50">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <!-- Contact Hero Section -->
    <section class="text-center py-20 bg-primary-100 relative">
        <div class="container mx-auto relative z-10">
            <h1 class="text-5xl font-bold text-primary-800 mb-4">Get in Touch</h1>
            <p class="text-xl text-primary-700 max-w-3xl mx-auto">
                We'd love to hear from you. Whether you have a question, a comment, or just want to say hello, we're here to help.
            </p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-20">
        <div class="container mx-auto grid md:grid-cols-2 gap-12 items-start">
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Send us a Message</h2>
                <form action="contact-handler.php" method="post">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" id="name" name="name" required class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" required class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        Send Message
                    </button>
                </form>
            </div>
            <div class="pt-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Contact Information</h3>
                <p class="text-lg text-gray-600 mb-6">
                    You can also reach us at the following email address. We'll get back to you as soon as possible.
                </p>
                <div class="flex items-center mb-4">
                    <span class="material-icons text-primary-600 text-3xl mr-4">email</span>
                    <a href="mailto:info@safehavendutch.com" class="text-lg text-gray-800 hover:text-primary-600">info@safehavendutch.com</a>
                </div>
            </div>
        </div>
    </section>

</body>

<?php
include 'footer.php';
?>
</html>
