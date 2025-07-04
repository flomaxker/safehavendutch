<?php
$page_title = "Dashboard";
require_once __DIR__ . '/header.php';

// --- Data Fetching ---
$pdo = $container->getPdo();



// 1. New Users Today vs Yesterday
$stmt_users_today = $pdo->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
$stmt_users_today->execute();
$new_users_today = $stmt_users_today->fetchColumn();

$stmt_users_yesterday = $pdo->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY");
$stmt_users_yesterday->execute();
$new_users_yesterday = $stmt_users_yesterday->fetchColumn();

// 2. Sales Revenue Today vs Yesterday
$stmt_sales_today = $pdo->prepare("SELECT SUM(amount_cents) FROM purchases WHERE DATE(purchased_at) = CURDATE()");
$stmt_sales_today->execute();
$sales_today = $stmt_sales_today->fetchColumn() ?: 0;

$stmt_sales_yesterday = $pdo->prepare("SELECT SUM(amount_cents) FROM purchases WHERE DATE(purchased_at) = CURDATE() - INTERVAL 1 DAY");
$stmt_sales_yesterday->execute();
$sales_yesterday = $stmt_sales_yesterday->fetchColumn() ?: 0;

// 3. Total Packages Sold (This remains a total, no comparison needed)
$stmt_packages = $pdo->prepare("SELECT COUNT(*) as count FROM purchases");
$stmt_packages->execute();
$total_packages_sold = $stmt_packages->fetchColumn();

// Helper function to calculate percentage change
function calculate_percentage_change($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0; // Avoid division by zero
    }
    return (($current - $previous) / $previous) * 100;
}

$user_change = calculate_percentage_change($new_users_today, $new_users_yesterday);
$sales_change = calculate_percentage_change($sales_today, $sales_yesterday);

// Helper function for relative time
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;

    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

    $parts = [
        'y' => $diff->y,
        'm' => $diff->m,
        'w' => $weeks,
        'd' => $days,
        'h' => $diff->h,
        'i' => $diff->i,
        's' => $diff->s,
    ];

    $result = [];
    foreach ($string as $key => $value) {
        if ($parts[$key] > 0) {
            $result[] = $parts[$key] . ' ' . $value . ($parts[$key] > 1 ? 's' : '');
        }
    }

    if (!$full) {
        $result = array_slice($result, 0, 1);
    }

    return $result ? implode(', ', $result) . ' ago' : 'just now';
}

// Fetch Recent User Registrations
$stmt_recent_users = $pdo->prepare("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$stmt_recent_users->execute();
$recent_users = $stmt_recent_users->fetchAll(PDO::FETCH_ASSOC);

// Fetch Recent Purchases
$stmt_recent_purchases = $pdo->prepare("SELECT p.id, u.name as user_name, u.id as user_id, p.amount_cents, p.purchased_at FROM purchases p JOIN users u ON p.user_id = u.id JOIN packages pk ON p.package_id = pk.id ORDER BY p.purchased_at DESC LIMIT 5");
$stmt_recent_purchases->execute();
$recent_purchases = $stmt_recent_purchases->fetchAll(PDO::FETCH_ASSOC);

// Fetch Recent Bookings
$stmt_recent_bookings = $pdo->prepare("
    SELECT b.id, u.name as user_name, u.id as user_id, l.title as lesson_title, b.created_at 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN lessons l ON b.lesson_id = l.id
    ORDER BY b.created_at DESC 
    LIMIT 5
");
$stmt_recent_bookings->execute();
$recent_bookings = $stmt_recent_bookings->fetchAll(PDO::FETCH_ASSOC);

// Consolidate all recent activities
$all_activities = [];

foreach ($recent_users as $user) {
    $all_activities[] = [
        'type' => 'user_registration',
        'timestamp' => $user['created_at'],
        'details' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]
    ];
}

foreach ($recent_purchases as $purchase) {
    $all_activities[] = [
        'type' => 'purchase',
        'timestamp' => $purchase['purchased_at'],
        'details' => [
            'id' => $purchase['id'],
            'user_id' => $purchase['user_id'],
            'user_name' => $purchase['user_name'],
            'amount_cents' => $purchase['amount_cents'],
        ]
    ];
}

foreach ($recent_bookings as $booking) {
    $all_activities[] = [
        'type' => 'booking',
        'timestamp' => $booking['created_at'],
        'details' => [
            'id' => $booking['id'],
            'user_id' => $booking['user_id'],
            'user_name' => $booking['user_name'],
            'lesson_title' => $booking['lesson_title'],
        ]
    ];
}


// Sort all activities by timestamp in descending order
usort($all_activities, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

// Limit to top 10 recent activities
$all_activities = array_slice($all_activities, 0, 10);

// Quick Actions
$user_id = $_SESSION['user_id'] ?? null;
$quick_actions_order = [];

if ($user_id) {
    $user_model = new \App\Models\User($container->getPdo());
    $user = $user_model->find($user_id);
    if ($user && !empty($user['quick_actions_order'])) {
        $quick_actions_order = json_decode($user['quick_actions_order'], true);
    }
}

// Define all possible quick actions from admin navigation
$all_quick_actions = [];
foreach ($nav_links as $link) {
    if (isset($link['children'])) {
        foreach ($link['children'] as $child) {
            $all_quick_actions[$child['url']] = [
                'icon' => $child['icon'],
                'text' => $child['title'],
                'url' => $child['url'],
            ];
        }
    } else {
        // Only include top-level links that are not the dashboard itself
        if ($link['url'] !== '/admin/index.php') {
            $all_quick_actions[$link['url']] = [
                'icon' => $link['icon'],
                'text' => $link['title'],
                'url' => $link['url'],
            ];
        }
    }
}

// Filter and sort quick actions based on user's saved order
$quick_actions = [];
if (!empty($quick_actions_order)) {
    foreach ($quick_actions_order as $ordered_url) {
        if (isset($all_quick_actions[$ordered_url])) {
            $quick_actions[$ordered_url] = $all_quick_actions[$ordered_url];
        }
    }
} else {
    // Default quick actions if none are saved (e.g., first 4 from all_quick_actions)
    $quick_actions = array_slice($all_quick_actions, 0, 4, true);
}

// --- UI Display ---
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 w-12 h-12 flex items-center justify-center rounded-xl mr-4">
                    <span class="material-icons text-3xl">person_add</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">New Users (Today)</p>
                    <div class="flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900"><?php echo $new_users_today; ?></p>
                        <span class="text-sm font-semibold ml-2 <?php echo $user_change >= 0 ? 'text-green-500' : 'text-red-500'; ?>">
                            <?php echo ($user_change >= 0 ? '+' : '') . number_format($user_change, 1); ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center">
                <div class="bg-green-100 text-green-600 w-12 h-12 flex items-center justify-center rounded-xl mr-4">
                    <span class="material-icons text-3xl">attach_money</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Sales Revenue (Today)</p>
                    <div class="flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">€<?php echo number_format($sales_today / 100, 2); ?></p>
                        <span class="text-sm font-semibold ml-2 <?php echo $sales_change >= 0 ? 'text-green-500' : 'text-red-500'; ?>">
                            <?php echo ($sales_change >= 0 ? '+' : '') . number_format($sales_change, 1); ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <div class="flex items-center">
                <div class="bg-purple-100 text-purple-600 w-12 h-12 flex items-center justify-center rounded-xl mr-4">
                    <span class="material-icons text-3xl">redeem</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Packages Sold</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $total_packages_sold; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-2xl shadow-lg mb-8">
        <div class="flex justify-end mb-4">
            <button id="openQuickActionsModalBtn" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition text-sm">Rearrange</button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php foreach ($quick_actions as $action): ?>
                <a href="<?php echo $action['url']; ?>" class="flex flex-col items-center justify-center p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors duration-200">
                    <span class="material-icons text-3xl text-gray-600 mb-2"><?php echo $action['icon']; ?></span>
                    <span class="text-sm font-medium text-center text-gray-700"><?php echo $action['text']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chart and Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Business Overview Chart (takes 2/3 width on large screens) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-2 md:mb-0">Business Overview</h2>
                <div class="flex items-center space-x-2">
                    <select id="chartDataType" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="revenue" selected>Sales Volume</option>
                        <option value="users">New User Signups</option>
                        <option value="bookings">Lessons Booked</option>
                        <option value="packages_sold">Packages Sold</option>
                    </select>
                    <select id="chartTimePeriod" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="30" selected>Last 30 Days</option>
                        <option value="60">Last 60 Days</option>
                        <option value="90">Last 90 Days</option>
                    </select>
                </div>
            </div>
            <div class="relative h-96">
                <div id="chartLoader" class="chart-loader" style="display: none;"></div>
                <canvas id="businessChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity (takes 1/3 width on large screens) -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Activity</h2>
            <?php if (!empty($all_activities)): ?>
                <div class="relative border-l-2 border-gray-200 ml-3">
                    <div class="space-y-8">
                        <?php foreach ($all_activities as $activity): ?>
                            <div class="relative">
                                <div class="absolute -left-4 top-1 flex items-center justify-center w-6 h-6 rounded-full
                                    <?php if ($activity['type'] === 'user_registration'): ?> bg-blue-500
                                    <?php elseif ($activity['type'] === 'purchase'): ?> bg-green-500
                                    <?php elseif ($activity['type'] === 'booking'): ?> bg-purple-500
                                    <?php endif; ?>
                                ">
                                    <span class="material-icons text-white text-sm">
                                        <?php if ($activity['type'] === 'user_registration'): ?>person_add
                                        <?php elseif ($activity['type'] === 'purchase'): ?>shopping_cart
                                        <?php elseif ($activity['type'] === 'booking'): ?>event_available
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="ml-10">
                                    <div class="flex justify-between items-center">
                                        <p class="font-medium text-gray-800 text-sm">
                                            <?php if ($activity['type'] === 'user_registration'): ?>
                                                New User: <a href="/admin/users/edit.php?id=<?php echo $activity['details']['id']; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($activity['details']['name']); ?></a>
                                            <?php elseif ($activity['type'] === 'purchase'): ?>
                                                Purchase by <a href="/admin/users/edit.php?id=<?php echo $activity['details']['user_id']; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($activity['details']['user_name']); ?></a>
                                            <?php elseif ($activity['type'] === 'booking'): ?>
                                                <a href="/admin/bookings/index.php?highlight=<?php echo $activity['details']['id']; ?>" class="text-blue-600 hover:underline">New Booking</a> by <a href="/admin/users/edit.php?id=<?php echo $activity['details']['user_id']; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($activity['details']['user_name']); ?></a>
                                            <?php endif; ?>
                                        </p>
                                        <span class="text-xs text-gray-500 whitespace-nowrap pl-2"><?php echo time_ago($activity['timestamp']); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <?php if ($activity['type'] === 'user_registration'): ?>
                                            <?php echo htmlspecialchars($activity['details']['email']); ?>
                                        <?php elseif ($activity['type'] === 'purchase'): ?>
                                            Amount: €<?php echo number_format($activity['details']['amount_cents'] / 100, 2); ?>
                                        <?php elseif ($activity['type'] === 'booking'): ?>
                                            <?php echo htmlspecialchars($activity['details']['lesson_title']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No recent activity.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions Modal -->
    <div id="quickActionsModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-4xl transform transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Rearrange Quick Actions</h2>
                <button class="close-modal-btn text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Available Actions -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Available Actions</h3>
                    <div id="availableQuickActions" class="quick-action-list min-h-[200px] bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <?php
                        $available_actions = array_diff_key($all_quick_actions, $quick_actions);
                        foreach ($available_actions as $url => $action):
                        ?>
                            <div class="flex items-center bg-white p-3 rounded-lg shadow-sm mb-3 cursor-move border border-gray-200" data-url="<?php echo htmlspecialchars($url); ?>">
                                <span class="material-icons text-gray-500 mr-4"><?php echo $action['icon']; ?></span>
                                <span class="font-medium text-gray-700"><?php echo $action['text']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Selected Actions -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Selected Actions (Max 5)</h3>
                    <div id="selectedQuickActions" class="quick-action-list min-h-[200px] bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <?php foreach ($quick_actions as $url => $action): ?>
                            <div class="flex items-center bg-white p-3 rounded-lg shadow-sm mb-3 cursor-move border border-gray-200" data-url="<?php echo htmlspecialchars($url); ?>">
                                <span class="material-icons text-gray-500 mr-4"><?php echo $action['icon']; ?></span>
                                <span class="font-medium text-gray-700"><?php echo $action['text']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button class="close-modal-btn bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                <button id="saveQuickActionsBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Save Changes</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script nonce="<?php echo $nonce; ?>">
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded in admin/index.php');
            const quickActionsModal = document.getElementById('quickActionsModal');
            const openModalBtn = document.getElementById('openQuickActionsModalBtn');
            const closeModalBtns = document.querySelectorAll('.close-modal-btn');
            const availableList = document.getElementById('availableQuickActions');
            const selectedList = document.getElementById('selectedQuickActions');
            const saveQuickActionsBtn = document.getElementById('saveQuickActionsBtn');

            console.log('quickActionsModal:', quickActionsModal);
            console.log('openModalBtn:', openModalBtn);
            console.log('availableList:', availableList);
            console.log('selectedList:', selectedList);

            // Initialize Sortable.js for both lists
            if (availableList && selectedList) {
                new Sortable(availableList, {
                    group: 'quickActions', // set both lists to same group
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });

                new Sortable(selectedList, {
                    group: 'quickActions',
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });
                console.log('Sortable.js initialized for both lists.');
            }

            // Open modal
            if (openModalBtn) {
                openModalBtn.addEventListener('click', function() {
                    console.log('Open modal button clicked.');
                    quickActionsModal.classList.remove('hidden');
                    console.log('Modal hidden class removed.', quickActionsModal.classList.contains('hidden'));
                });
            }

            // Close modal
            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    console.log('Close modal button clicked.');
                    quickActionsModal.classList.add('hidden');
                    console.log('Modal hidden class added.', quickActionsModal.classList.contains('hidden'));
                });
            });

            // Close modal when clicking outside
            if (quickActionsModal) {
                quickActionsModal.addEventListener('click', function(event) {
                    if (event.target === quickActionsModal) {
                        console.log('Clicked outside modal.');
                        quickActionsModal.classList.add('hidden');
                    }
                });
            }

            // Save quick actions order
            if (saveQuickActionsBtn) {
                saveQuickActionsBtn.addEventListener('click', async function() {
                    console.log('Save button clicked.');
                    const orderedUrls = Array.from(selectedList.children).map(item => item.dataset.url); // Get URLs from selected list only
                    const userId = <?php echo json_encode($user_id); ?>; // Pass admin user ID from PHP
                    console.log('Ordered URLs (selected):', orderedUrls);
                    console.log('User ID:', userId);

                    try {
                        const response = await fetch('save_quick_actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order: orderedUrls, userId: userId })
                        });
                        const data = await response.json();

                        if (data.status === 'success') {
                            quickActionsModal.classList.add('hidden');
                            location.reload(); // Reload page to reflect new order
                        } else {
                            alert('Error saving quick actions: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while saving quick actions.');
                    }
                });
            }
        });
    </script>

    
    

    

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script nonce="<?php echo $nonce; ?>">
        document.addEventListener('DOMContentLoaded', function() {
            // Quick Actions Modal Logic
            const quickActionsModal = document.getElementById('quickActionsModal');
            const openModalBtn = document.getElementById('openQuickActionsModalBtn');
            const closeModalBtns = document.querySelectorAll('.close-modal-btn');
            const availableList = document.getElementById('availableQuickActions');
            const selectedList = document.getElementById('selectedQuickActions');
            const saveQuickActionsBtn = document.getElementById('saveQuickActionsBtn');

            if (availableList && selectedList) {
                new Sortable(availableList, {
                    group: 'quickActions',
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });

                new Sortable(selectedList, {
                    group: 'quickActions',
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });
            }

            if (openModalBtn) {
                openModalBtn.addEventListener('click', function() {
                    quickActionsModal.classList.remove('hidden');
                });
            }

            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    quickActionsModal.classList.add('hidden');
                });
            });

            if (quickActionsModal) {
                quickActionsModal.addEventListener('click', function(event) {
                    if (event.target === quickActionsModal) {
                        quickActionsModal.classList.add('hidden');
                    }
                });
            }

            if (saveQuickActionsBtn) {
                saveQuickActionsBtn.addEventListener('click', async function() {
                    const orderedUrls = Array.from(selectedList.children).map(item => item.dataset.url);
                    const userId = <?php echo json_encode($user_id); ?>;

                    try {
                        const response = await fetch('save_quick_actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order: orderedUrls, userId: userId })
                        });
                        const data = await response.json();

                        if (data.status === 'success') {
                            quickActionsModal.classList.add('hidden');
                            location.reload();
                        } else {
                            alert('Error saving quick actions: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while saving quick actions.');
                    }
                });
            }
        });
    </script>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
