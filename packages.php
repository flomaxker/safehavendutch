<?php
require_once __DIR__ . '/bootstrap.php';

use App\Models\Package;
use App\Models\Page;

$pageModel = $container->getPageModel();
$page = $pageModel->findBySlug('packages');

$packageModel = new Package($container->getPdo());
$packages = $packageModel->getAllActive();

$page_title = $page['title'] ?? 'Our Packages';
include 'header.php';
?>

<body class="bg-gray-50">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <!-- Packages Hero Section -->
    <section class="pt-24 bg-primary-100 relative">
        <div class="container mx-auto relative z-10 py-20 text-center">
            <h1 class="text-5xl font-bold text-primary-800 mb-4"><?php echo htmlspecialchars($page['title'] ?? 'Our Packages'); ?></h1>
            <p class="text-xl text-primary-700 max-w-3xl mx-auto">
                <?php echo htmlspecialchars($page['meta_description'] ?? 'Choose the perfect plan to start your journey with us. All packages include personalized coaching and access to our community.'); ?>
            </p>
        </div>
    </section>

    <div class="container mx-auto px-6 py-8">
        <p class="text-lg text-gray-700 leading-relaxed">
            <?php echo nl2br(htmlspecialchars($page['main_content'] ?? '')); ?>
        </p>
    </div>

    <!-- Pricing Section -->
    <section class="py-20">
        <div class="container mx-auto">
            <div class="grid lg:grid-cols-3 gap-8">
                <?php
                // Example: Highlight the second package. You can make this dynamic later.
                $highlight_index = 1; 
                foreach ($packages as $index => $package):
                    $is_highlighted = ($index === $highlight_index);
                ?>
                    <div class="relative flex flex-col rounded-2xl border <?php echo $is_highlighted ? 'border-primary-500' : 'border-gray-200'; ?> bg-white p-8 shadow-lg">
                        <?php if ($is_highlighted): ?>
                            <div class="absolute top-0 -translate-y-1/2 transform rounded-full bg-primary-500 px-4 py-1.5 text-sm font-semibold text-white">
                                Most Popular
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="text-2xl font-semibold leading-7 text-gray-900"><?php echo htmlspecialchars($package['name']); ?></h3>
                        <p class="mt-4 flex items-baseline gap-x-2">
                            <span class="text-5xl font-bold tracking-tight text-gray-900">&euro;<?php echo htmlspecialchars(number_format($package['price_cents'] / 100, 2)); ?></span>
                        </p>
                        <p class="mt-6 text-base leading-7 text-gray-600"><?php echo htmlspecialchars($package['description']); ?></p>
                        
                        <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                            <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none text-primary-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                                Includes <?php echo htmlspecialchars($package['euro_value']); ?> credits
                            </li>
                             <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none text-primary-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                                Personalized coaching
                            </li>
                             <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none text-primary-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                                Community access
                            </li>
                        </ul>

                        <a href="/checkout.php?package_id=<?php echo $package['id']; ?>" class="mt-8 block rounded-md <?php echo $is_highlighted ? 'bg-primary-600 text-white shadow-sm hover:bg-primary-500' : 'bg-gray-100 text-primary-600 hover:bg-gray-200'; ?> px-3 py-2 text-center text-sm font-semibold leading-6">
                            Get Started
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</body>

<?php
include 'footer.php';
?>
</html>
