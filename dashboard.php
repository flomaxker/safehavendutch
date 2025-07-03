<?php
$page_title = "Dashboard";
require_once __DIR__ . '/user_header.php';

$userModel = $container->getUserModel();
$bookingModel = $container->getBookingModel();
$childModel = $container->getChildModel();

$user_id = $_SESSION['user_id'];
$user_euro_balance = $userModel->getEuroBalance($user_id);
$user_email = $_SESSION['user_email'] ?? 'Guest';

$upcomingBookings = $bookingModel->getBookingsByUserId($user_id);
$children = $childModel->findByUserId($user_id);

$page_title = "Dashboard";

?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, <?php echo htmlspecialchars($user_email); ?>!</h1>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 w-12 h-12 flex items-center justify-center rounded-xl mr-4">
                    <span class="material-icons text-3xl">account_balance_wallet</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Your Euro Balance</p>
                    <p class="text-3xl font-bold text-gray-900">€<?php echo htmlspecialchars(number_format($user_euro_balance / 100, 2)); ?></p>
                </div>
            </div>
        </div>
        <!-- More dashboard widgets can be added here -->
    </div>

    <!-- My Children -->
    <div class="bg-white p-6 rounded-2xl shadow-lg mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">My Children</h2>
        <?php if (empty($children)): ?>
            <p class="text-gray-600">You have not added any children yet.</p>
            <a href="my_children.php" class="text-primary-600 hover:underline">Add a child</a>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date of Birth</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($children as $child): ?>
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($child['name']); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($child['date_of_birth']); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($child['notes']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upcoming Lessons -->
    <div class="bg-white p-6 rounded-2xl shadow-lg mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Upcoming Lessons</h2>
        <?php if (empty($upcomingBookings)): ?>
            <p class="text-gray-600">You have no upcoming lessons.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lesson</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teacher</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcomingBookings as $booking): ?>
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($booking['lesson_title']); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($booking['teacher_name']); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['start_time']))); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(date('H:i', strtotime($booking['start_time']))) . ' - ' . htmlspecialchars(date('H:i', strtotime($booking['end_time']))); ?></td>
                                <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    