<?php
session_start();

require_once __DIR__ . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

$purchaseModel = $container->getPurchaseModel();
$purchases = $purchaseModel->getByUserId($userId);

$page_title = "Purchase History";
include 'header.php';
?>

<div class="container mx-auto p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-8 mt-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Your Purchase History</h1>

        <?php if (empty($purchases)): ?>
            <p class="text-gray-600">You haven't made any purchases yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Package</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Credits</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($purchase['created_at']))); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($purchase['package_name']); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($purchase['credit_amount']); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200">&euro;<?php echo htmlspecialchars(number_format($purchase['amount_cents'] / 100, 2)); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(ucfirst($purchase['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php';
?>