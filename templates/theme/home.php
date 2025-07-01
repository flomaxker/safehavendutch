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
        <section class="mt-16 md:mt-24 flex flex-col md:flex-row items-center text-center md:text-left gap-y-16 md:gap-x-16">
            <div class="md:w-1/2 relative">
                <h1 class="text-5xl md:text-6xl font-bold leading-tight">
                    The best place to
                    <span class="scribble-learn text-primary-600">learn</span> and <span class="scribble-play text-secondary-500">integrate</span>
                    in the Netherlands
                </h1>
                <p class="mt-6 text-gray-600 text-lg">
                    Discover personalized coaching and practical support to help you thrive in your new Dutch life.
                </p>
                <a href="/packages.php" class="mt-8 inline-block bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-full font-semibold text-lg shadow-md transition duration-300">
                    Explore Packages <span class="material-icons align-middle ml-1 text-xl">arrow_forward</span>
                </a>
            </div>
            <div class="md:w-1/2 flex justify-center items-center relative">
                <div class="hero-image-card w-full max-w-sm aspect-[3/4] transform rotate-2">
                    <img src="<?= htmlspecialchars($heroImage) ?>" alt="A welcoming scene from the Netherlands" class="w-full h-full object-cover" />
                </div>
            </div>
        </section>

        <section class="home-section">
            <div class="container mx-auto">
                <h2 class="text-4xl font-bold text-center mb-12">Our <span class="text-primary-600">supportive</span> features</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="feature-card bg-primary-100 text-center">
                        <div class="feature-icon mx-auto">
                            <span class="material-icons">school</span>
                        </div>
                        <h3 class="text-2xl font-semibold text-primary-800 mb-2">Language Coaching</h3>
                        <p class="text-gray-600">
                            Go beyond grammar with personalized coaching that builds real-world confidence.
                        </p>
                    </div>
                    <div class="feature-card bg-secondary-100 text-center">
                        <div class="feature-icon mx-auto" style="background-color: #ccfbf1;">
                            <span class="material-icons text-secondary-600">support_agent</span>
                        </div>
                        <h3 class="text-2xl font-semibold text-secondary-800 mb-2">Integration Help</h3>
                        <p class="text-gray-600">
                            Get practical support for housing, healthcare, and navigating Dutch culture.
                        </p>
                    </div>
                    <div class="feature-card bg-yellow-100 text-center">
                         <div class="feature-icon mx-auto" style="background-color: #fef9c3;">
                            <span class="material-icons text-yellow-600">groups</span>
                        </div>
                        <h3 class="text-2xl font-semibold text-yellow-800 mb-2">Community Events</h3>
                        <p class="text-gray-600">
                            Connect with other expats and practice your Dutch in a fun, relaxed setting.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php
// We still need the footer
include 'footer.php';
?>
</body>
</html>
