<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once 'bootstrap.php';
include 'header.php';

$packageModel = $container->getPackageModel();
$packages = $packageModel->getAllActive();

?>

<div class="container mx-auto p-4">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Available Packages</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($packages as $package): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <h5 class="text-xl font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($package['name']); ?></h5>
                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($package['description']); ?></p>
                    <p class="text-gray-700 mb-2"><strong>Euros:</strong> <?php echo htmlspecialchars($package['euro_value']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>Price:</strong> &euro;<?php echo htmlspecialchars(number_format($package['price_cents'] / 100, 2)); ?></p>
                    <a href="/checkout.php?package_id=<?php echo $package['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">Purchase</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
include 'footer.php';
?>