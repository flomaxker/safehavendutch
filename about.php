<?php
require_once __DIR__ . '/bootstrap.php';
use App\Models\Page;

$pageModel = $container->getPageModel();
$page = $pageModel->findBySlug('about');

$page_title = $page['title'] ?? 'About Us';

include 'header.php';
?>

<body class="bg-gray-50">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <!-- About Us Hero Section -->
    <section class="pt-24 bg-primary-100 relative">
        <div class="container mx-auto relative z-10 py-20 text-center">
            <h1 class="text-5xl font-bold text-primary-800 mb-4"><?php echo htmlspecialchars($page['about_hero_title'] ?? 'About Safe Haven Dutch'); ?></h1>
            <p class="text-xl text-primary-700 max-w-3xl mx-auto">
                <?php echo htmlspecialchars($page['about_hero_subtitle'] ?? ''); ?>
            </p>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="py-20">
        <div class="container mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($page['about_mission_heading'] ?? 'Our Mission'); ?></h2>
                <p class="text-lg text-gray-600 mb-4">
                    <?php echo nl2br(htmlspecialchars($page['about_mission_text'] ?? '')); ?>
                </p>
            </div>
            <div>
                <img src="<?php echo htmlspecialchars($page['about_mission_image'] ?? '/assets/images/Holistic.jpg'); ?>" alt="A group of people collaborating" class="rounded-lg shadow-xl">
            </div>
        </div>
    </section>

    <!-- Founder Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-12"><?php echo htmlspecialchars($page['about_founder_heading'] ?? 'Meet the Founder'); ?></h2>
            <div class="max-w-xl mx-auto">
                <img src="<?php echo htmlspecialchars($page['about_founder_image'] ?? '/assets/images/teacher-photo.jpg'); ?>" alt="Founder of Safe Haven Dutch" class="w-40 h-40 rounded-full mx-auto mb-6 shadow-lg">
                <h3 class="text-3xl font-semibold mb-2"><?php echo htmlspecialchars($page['about_founder_name'] ?? 'Jane Doe'); ?></h3>
                <p class="text-primary-600 font-medium mb-4"><?php echo htmlspecialchars($page['about_founder_title'] ?? 'Founder & Lead Coach'); ?></p>
                <p class="text-gray-600 italic">
                    <?php echo htmlspecialchars($page['about_founder_quote'] ?? ''); ?>
                </p>
            </div>
        </div>
    </section>

</body>

<?php
include 'footer.php';
?>
</html>
