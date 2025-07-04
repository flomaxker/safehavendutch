<?php
$page_title = "My Bookings";
require_once __DIR__ . '/user_header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userModel = $container->getUserModel();

// Get the user's role
$loggedInUser = $userModel->find($_SESSION['user_id']);
if ($loggedInUser && $loggedInUser['role'] === 'admin') {
    header('Location: /admin/index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$bookingModel = $container->getBookingModel();
$bookings = $bookingModel->findByUser($userId);
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Bookings</h1>

    <?php if (empty($bookings)): ?>
        <p class="text-gray-600">You have no upcoming or past bookings.</p>
    <?php else: ?>
        <div class="overflow-x-auto bg-white p-6 rounded-2xl shadow-lg">
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
                    <?php foreach ($bookings as $booking): ?>
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