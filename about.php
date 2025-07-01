<?php
require_once __DIR__ . '/bootstrap.php';
use App\Models\Page;

$pageModel = $container->getPageModel();
$page = $pageModel->findBySlug('about');

// Default content in case the database fetch fails or is empty
$page_title = 'About Us';
$page_content = 'Default about content.';

if ($page) {
    $page_title = $page['title'];
    // We will structure the content ourselves, but can use parts of the fetched content if needed.
    // For this redesign, we are creating a new layout.
}

include 'header.php';
?>

<body class="bg-gray-50">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <!-- About Us Hero Section -->
    <section class="text-center py-20 bg-primary-100 relative">
        <div class="container mx-auto relative z-10">
            <h1 class="text-5xl font-bold text-primary-800 mb-4">About Safe Haven Dutch</h1>
            <p class="text-xl text-primary-700 max-w-3xl mx-auto">
                We're dedicated to helping you feel at home in the Netherlands through language, culture, and community.
            </p>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="py-20">
        <div class="container mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Our Mission</h2>
                <p class="text-lg text-gray-600 mb-4">
                    Moving to a new country is more than just learning the language. It's about finding your footing, understanding the culture, and building a new life. Our mission is to provide a supportive and friendly "safe haven" where you can do just that.
                </p>
                <p class="text-lg text-gray-600">
                    We combine expert language coaching with practical integration support to empower you on your journey.
                </p>
            </div>
            <div>
                <img src="/assets/images/Holistic.jpg" alt="A group of people collaborating" class="rounded-lg shadow-xl">
            </div>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12">Meet the Founder</h2>
            <div class="max-w-xl mx-auto">
                <img src="/assets/images/teacher-photo.jpg" alt="Founder of Safe Haven Dutch" class="w-40 h-40 rounded-full mx-auto mb-6 shadow-lg">
                <h3 class="text-3xl font-semibold mb-2">Jane Doe</h3>
                <p class="text-primary-600 font-medium mb-4">Founder & Lead Coach</p>
                <p class="text-gray-600 italic">
                    "Having lived abroad myself, I know the challenges and triumphs of starting over in a new place. I created Safe Haven Dutch to be the resource I wished I had—a place of warmth, understanding, and genuine support. Welcome!"
                </p>
            </div>
        </div>
    </section>

</body>

<?php
include 'footer.php';
?>
</html>
