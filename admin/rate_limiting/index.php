<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;

$page_title = "Rate Limiting Monitoring";
include __DIR__ . '/../header.php';

$userModel = new User($container->getPdo());

// Fetch recent failed login attempts
$stmt = $container->getPdo()->query(
    'SELECT la.ip_address, u.email, la.attempted_at 
     FROM login_attempts la
     JOIN users u ON la.user_id = u.id
     WHERE la.successful = 0 AND la.attempted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
     ORDER BY la.attempted_at DESC'
);
$failed_attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="px-4 py-8">
        <div class="mb-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Rate Limiting Monitoring</h1>
            <p class="text-gray-600">This page shows recent failed login attempts to help monitor potential brute-force attacks.</p>
        </div>
        <div class="my-5">
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">Recent Failed Logins (Last Hour)</h3>
                </div>
                <div class="p-0">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    User Email
                                </th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Time of Attempt
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($failed_attempts)): ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No failed login attempts in the last hour.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($failed_attempts as $attempt): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">
                                            <?= htmlspecialchars($attempt['ip_address']) ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">
                                            <?= htmlspecialchars($attempt['email']) ?>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm">
                                            <?= htmlspecialchars($attempt['attempted_at']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
