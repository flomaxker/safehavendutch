<?php
$page_title = "Purchase History";
require_once __DIR__ . '/user_header.php';

$userId = $_SESSION['user_id'];

$purchaseModel = $container->getPurchaseModel();
$purchases = $purchaseModel->getByUserId($userId);
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Your Purchase History</h1>

    <?php if (empty($purchases)): ?>
        <p class="text-gray-600">You haven't made any purchases yet.</p>
    <?php else: ?>
        <div class="overflow-x-auto bg-white p-6 rounded-2xl shadow-lg">
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
                            <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($purchase['euro_value']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200">&euro;<?php echo htmlspecialchars(number_format($purchase['amount_cents'] / 100, 2)); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(ucfirst($purchase['status'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

