<?php
// Home page template - Redesigned based on WonderKids example
use App\Models\Setting;

// Fetch settings for hero image
$settingModel = new Setting($container->getPdo());
$settings = $settingModel->getAllSettings();
$heroImage = $settings['hero_image'] ?? '/assets/images/default-hero.jpg';

include 'header.php'; // We still need the header for nav, etc.
?>

<body class="overflow-x-hidden">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <div class="container mx-auto px-6 py-8 relative z-10">
        <section class="mt-16 md:mt-24 mb-16 flex flex-col md:flex-row items-center text-center md:text-left gap-y-16 md:gap-x-16">
            <div class="md:w-1/2 relative">
                <h1 class="text-5xl md:text-6xl font-bold leading-tight">
                    <?php echo htmlspecialchars($page['hero_title'] ?? ''); ?>
                </h1>
                <p class="mt-6 text-gray-600 text-lg">
                    <?php echo htmlspecialchars($page['hero_subtitle'] ?? ''); ?>
                </p>
                <a href="/packages.php" class="mt-8 inline-block bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-full font-semibold text-lg shadow-md transition duration-300">
                    Explore Packages <span class="material-icons align-middle ml-1 text-xl">arrow_forward</span>
                </a>
            </div>
            <div class="md:w-1/2 flex justify-center items-center relative">
                <div class="hero-image-card w-full max-w-lg aspect-[3/4]">
                    <img src="<?= htmlspecialchars($heroImage) ?>" alt="A welcoming scene from the Netherlands" class="w-full h-full object-cover" />
                </div>
            </div>
        </section>

        </section>

        <section class="home-section">
            <div class="container mx-auto">
                <h2 class="text-4xl font-bold text-center mb-12"><?php echo htmlspecialchars($page['features_heading'] ?? 'Our Supportive Features'); ?></h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="feature-card bg-primary-100 text-center">
                        <div class="feature-icon mx-auto">
                            <span class="material-icons"><?php echo htmlspecialchars($page['feature1_icon'] ?? ''); ?></span>
                        </div>
                        <h3 class="text-2xl font-semibold text-primary-800 mb-2"><?php echo htmlspecialchars($page['feature1_title'] ?? ''); ?></h3>
                        <p class="text-gray-600">
                            <?php echo nl2br(htmlspecialchars($page['feature1_description'] ?? '')); ?>
                        </p>
                    </div>
                    <div class="feature-card bg-secondary-100 text-center">
                        <div class="feature-icon mx-auto" style="background-color: #ccfbf1;">
                            <span class="material-icons text-secondary-600"><?php echo htmlspecialchars($page['feature2_icon'] ?? ''); ?></span>
                        </div>
                        <h3 class="text-2xl font-semibold text-secondary-800 mb-2"><?php echo htmlspecialchars($page['feature2_title'] ?? ''); ?></h3>
                        <p class="text-gray-600">
                            <?php echo nl2br(htmlspecialchars($page['feature2_description'] ?? '')); ?>
                        </p>
                    </div>
                    <div class="feature-card bg-white text-center">
                         <div class="feature-icon mx-auto">
                            <span class="material-icons"><?php echo htmlspecialchars($page['feature3_icon'] ?? ''); ?></span>
                        </div>
                        <h3 class="text-2xl font-semibold text-primary-800 mb-2"><?php echo htmlspecialchars($page['feature3_title'] ?? ''); ?></h3>
                        <p class="text-gray-600">
                            <?php echo nl2br(htmlspecialchars($page['feature3_description'] ?? '')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-section">
            <div class="container mx-auto">
                <p class="text-lg text-gray-700 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($page['main_content'] ?? '')); ?>
                </p>
            </div>
        </section>

        <?php if (!empty($page['show_contact_form'])): ?>
            <!-- Contact Form HTML will go here -->
            <section class="home-section">
                <div class="container mx-auto">
                    <h2 class="text-4xl font-bold text-center mb-12">Contact Us</h2>
                    <p class="text-center text-lg text-gray-600 mb-8">Please replace this with your actual contact form HTML.</p>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($page['show_packages'])): ?>
            <!-- Packages/Pricing Grid HTML will go here -->
            <section class="home-section">
                <div class="container mx-auto">
                    <h2 class="text-4xl font-bold text-center mb-12">Our Packages</h2>
                    <p class="text-center text-lg text-gray-600 mb-8">Please replace this with your actual packages/pricing grid HTML.</p>
                </div>
            </section>
        <?php endif; ?>
    </div>

<?php
// We still need the footer
include 'footer.php';
?>
</body>
</html>
