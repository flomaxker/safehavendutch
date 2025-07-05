<?php
$page_title = "Dashboard";
require_once __DIR__ . '/user_header.php';

$userModel = $container->getUserModel();
$bookingModel = $container->getBookingModel();
$childModel = $container->getChildModel();

$user_id = $_SESSION['user_id'];
$user = $userModel->find($user_id);
$user_euro_balance = $user['euro_balance'];
$user_name = $user['name'];

$upcomingBookings = $bookingModel->findUpcomingWithDetailsByUserId($user_id);
$children = $childModel->findByUserId($user_id);

// Define user-specific quick actions
$quick_actions = [
    ['title' => 'Book a Lesson', 'url' => '/book_lesson.php', 'icon' => 'school'],
    ['title' => 'My Bookings', 'url' => '/my_bookings.php', 'icon' => 'event_note'],
    ['title' => 'My Children', 'url' => '/my_children.php', 'icon' => 'child_care'],
    ['title' => 'My Profile', 'url' => '/my_profile.php', 'icon' => 'person'],
    ['title' => 'Purchase History', 'url' => '/purchase_history.php', 'icon' => 'history'],
];

$page_title = "Dashboard";

?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Row: Euro Balance and Quick Actions -->
        <div class="lg:col-span-2 flex flex-col md:flex-row gap-8">
            <!-- Top-Left: Euro Balance -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-2xl shadow-lg flex flex-col items-center justify-center text-center w-full md:w-64 flex-shrink-0">
                <div class="mb-2">
                    <div class="bg-white bg-opacity-20 text-white w-12 h-12 flex items-center justify-center rounded-full mx-auto mb-2">
                        <span class="material-icons text-3xl">account_balance_wallet</span>
                    </div>
                    <p class="text-xs font-medium opacity-80">Your Euro Balance</p>
                    <p class="text-4xl font-bold mt-0">€<?php echo htmlspecialchars(number_format($user_euro_balance / 100, 2)); ?></p>
                </div>
                <div>
                    <a href="/packages.php" class="inline-flex items-center px-4 py-1 border border-white border-opacity-50 text-xs font-medium rounded-full shadow-sm text-white bg-white bg-opacity-20 hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white transition">
                        <span class="material-icons -ml-1 mr-1 text-base">add_circle_outline</span>
                        Top Up Balance
                    </a>
                </div>
            </div>

            <!-- Top-Right: Quick Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-lg flex-grow">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <?php foreach ($quick_actions as $action): ?>
                        <a href="<?php echo $action['url']; ?>" class="flex flex-col items-center justify-center p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors duration-200">
                            <span class="material-icons text-3xl text-gray-600 mb-2"><?php echo $action['icon']; ?></span>
                            <span class="text-sm font-medium text-center text-gray-700"><?php echo $action['title']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Row: My Children and Upcoming Lessons -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Bottom-Left: My Children -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">My Children</h2>
                <?php if (empty($children)):
 ?>
                    <p class="text-gray-600">You have not added any children yet.</p>
                    <a href="my_children.php" class="text-primary-600 hover:underline">Add a child</a>
                <?php else:
 ?>
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

            <!-- Bottom-Right: Upcoming Lessons -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Upcoming Lessons</h2>
                <?php if (empty($upcomingBookings)):
 ?>
                    <p class="text-gray-600">You have no upcoming lessons.</p>
                <?php else:
 ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lesson</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teacher</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingBookings as $booking): ?>
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($booking['lesson_title']); ?></td>
                                        <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars($booking['teacher_name']); ?></td>
                                        <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(date('D, M j, Y', strtotime($booking['start_time']))); ?></td>
                                        <td class="py-2 px-4 border-b border-gray-200"><?php echo htmlspecialchars(date('g:i A', strtotime($booking['start_time']))) . ' - ' . htmlspecialchars(date('g:i A', strtotime($booking['end_time']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    

</div>



    