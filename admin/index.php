<?php
$page_title = "Dashboard";
require_once __DIR__ . '/header.php';

// --- Data Fetching ---
$pdo = $container->getPdo();

// 1. New Users Today
$stmt_users = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()");
$stmt_users->execute();
$new_users_today = $stmt_users->fetchColumn();

// 2. Sales Revenue Today
$stmt_sales = $pdo->prepare("SELECT SUM(amount_cents) as total_sales FROM purchases WHERE DATE(purchased_at) = CURDATE()");
$stmt_sales->execute();
$sales_today = $stmt_sales->fetchColumn() ?: 0;

// 3. Total Packages Sold
$stmt_packages = $pdo->prepare("SELECT COUNT(*) as count FROM purchases");
$stmt_packages->execute();
$total_packages_sold = $stmt_packages->fetchColumn();

// 4. Recent User Registrations
$stmt_recent_users = $pdo->prepare("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$stmt_recent_users->execute();
$recent_users = $stmt_recent_users->fetchAll(PDO::FETCH_ASSOC);

// 5. Recent Purchases
$stmt_recent_purchases = $pdo->prepare("SELECT u.name as user_name, p.amount_cents, p.purchased_at FROM purchases p JOIN users u ON p.user_id = u.id ORDER BY p.purchased_at DESC LIMIT 5");
$stmt_recent_purchases->execute();
$recent_purchases = $stmt_recent_purchases->fetchAll(PDO::FETCH_ASSOC);

// 6. Recent Bookings
$stmt_recent_bookings = $pdo->prepare("
    SELECT b.id, u.name as user_name, l.title as lesson_title, b.created_at 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN lessons l ON b.lesson_id = l.id
    ORDER BY b.created_at DESC 
    LIMIT 5
");
$stmt_recent_bookings->execute();
$recent_bookings = $stmt_recent_bookings->fetchAll(PDO::FETCH_ASSOC);

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
                    <p class="text-3xl font-bold text-gray-900"><?php echo $new_users_today; ?></p>
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
                    <p class="text-3xl font-bold text-gray-900">€<?php echo number_format($sales_today / 100, 2); ?></p>
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
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Quick Actions</h2>
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

    <!-- Quick Actions Modal -->
    <div id="quickActionsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Rearrange Quick Actions</h3>
                <button class="close-modal-btn text-gray-400 hover:text-gray-500">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="mt-2 grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-md font-semibold text-gray-700 mb-2">Available Actions</h4>
                    <ul id="availableQuickActions" class="space-y-2 min-h-[100px] border border-gray-300 p-2 rounded-md bg-gray-50">
                        <?php
                        $selectedUrls = array_column($quick_actions, 'url');
                        foreach ($all_quick_actions as $key => $action) :
                            if (!in_array($action['url'], $selectedUrls)) :
                        ?>
                                <li class="bg-gray-100 p-3 rounded-md flex items-center justify-between cursor-grab" data-url="<?= $action['url'] ?>">
                                    <div class="flex items-center">
                                        <span class="material-icons mr-3"><?= $action['icon'] ?></span>
                                        <span><?= $action['text'] ?></span>
                                    </div>
                                    <span class="material-icons text-gray-400">drag_indicator</span>
                                </li>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-gray-700 mb-2">Selected Actions</h4>
                    <ul id="selectedQuickActions" class="space-y-2 min-h-[100px] border border-gray-300 p-2 rounded-md bg-gray-50">
                        <?php foreach ($quick_actions as $action) : ?>
                            <li class="bg-gray-100 p-3 rounded-md flex items-center justify-between cursor-grab" data-url="<?= $action['url'] ?>">
                                <div class="flex items-center">
                                    <span class="material-icons mr-3"><?= $action['icon'] ?></span>
                                    <span><?= $action['text'] ?></span>
                                </div>
                                <span class="material-icons text-gray-400">drag_indicator</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button id="saveQuickActionsBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Save Order</button>
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

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent User Registrations</h2>
            <?php if (!empty($recent_users)): ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($recent_users as $user): ?>
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($user['name']); ?></p>
                                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <span class="text-gray-500 text-sm"><?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-600">No recent user registrations.</p>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Purchases</h2>
            <?php if (!empty($recent_purchases)): ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($recent_purchases as $purchase): ?>
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 font-medium">Purchase by <?php echo htmlspecialchars($purchase['user_name']); ?></p>
                                <p class="text-gray-600 text-sm">€<?php echo number_format($purchase['amount_cents'] / 100, 2); ?></p>
                            </div>
                            <span class="text-gray-500 text-sm"><?php echo date('M d, Y H:i', strtotime($purchase['purchased_at'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-600">No recent purchases.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Bookings</h2>
            <?php if (!empty($recent_bookings)): ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($recent_bookings as $booking): ?>
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 font-medium">
                                    <a href="/admin/bookings/index.php?highlight=<?= $booking['id'] ?>" class="text-blue-600 hover:underline">
                                        Booking #<?= $booking['id'] ?>
                                    </a>
                                    for "<?= htmlspecialchars($booking['lesson_title']) ?>"
                                </p>
                                <p class="text-gray-600 text-sm">
                                    Booked by <?= htmlspecialchars($booking['user_name']) ?>
                                </p>
                            </div>
                            <span class="text-gray-500 text-sm"><?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-600">No recent bookings.</p>
            <?php endif; ?>
        </div>
    </div>

</div>