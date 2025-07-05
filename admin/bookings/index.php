<?php
require_once __DIR__ . '/../../bootstrap.php';

// Admin-only access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$bookingModel = $container->getBookingModel();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && isset($_POST['selected_bookings']) && is_array($_POST['selected_bookings'])) {
        $ids = array_map('intval', $_POST['selected_bookings']);
        try {
            if ($bookingModel->deleteMany($ids)) {
                $message = count($ids) . ' bookings deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete selected bookings.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    header('Location: index.php?message=' . urlencode($message) . '&type=' . urlencode($messageType));
    exit;
}

// Check for messages from redirect
if (isset($_GET['message'], $_GET['type'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = htmlspecialchars($_GET['type']);
}

// Initialize sorting parameters
$order_by = $_GET['order_by'] ?? 'id';
$order_direction = $_GET['order_direction'] ?? 'ASC';

$bookings = $bookingModel->findAllWithDetails($order_by, $order_direction);

$page_title = 'Manage Bookings';

include __DIR__ . '/../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Bookings</h1>
        <p class="text-gray-500">View and manage all bookings.</p>
    </div>
</header>

<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
    <form method="post" id="bulkActionsForm">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <input type="checkbox" id="selectAllBookings" class="form-checkbox h-4 w-4 text-blue-600">
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=id&order_direction=<?= ($order_by === 'id' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            ID
                            <?php if ($order_by === 'id'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=lesson_id&order_direction=<?= ($order_by === 'lesson_id' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Lesson
                            <?php if ($order_by === 'lesson_id'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=user_id&order_direction=<?= ($order_by === 'user_id' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            User
                            <?php if ($order_by === 'user_id'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=created_at&order_direction=<?= ($order_by === 'created_at' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Booking Date
                            <?php if ($order_by === 'created_at'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr class="<?php if (isset($_GET['highlight']) && $_GET['highlight'] == $booking['id']) echo 'bg-yellow-100'; ?>">
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <input type="checkbox" name="selected_bookings[]" value="<?= $booking['id'] ?>" class="booking-checkbox form-checkbox h-4 w-4 text-blue-600">
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($booking['id']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($booking['lesson_title']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($booking['user_name']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($booking['created_at']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="delete.php?id=<?= $booking['id'] ?>" class="text-red-600 hover:text-red-900">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            No bookings found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="px-5 py-3 bg-gray-100 border-t border-gray-200 flex justify-end">
            <button type="submit" name="action" value="bulk_delete" id="bulkDeleteBtn" class="text-red-600 border border-red-600 py-1 px-2 rounded-md hover:bg-red-50 transition text-xs opacity-50 cursor-not-allowed" disabled onclick="return confirm('Are you sure you want to delete selected bookings?');">Bulk Delete Selected</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllBookings');
        const bookingCheckboxes = document.querySelectorAll('.booking-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        function updateBulkDeleteButtonState() {
            const anyChecked = Array.from(bookingCheckboxes).some(checkbox => checkbox.checked);
            if (anyChecked) {
                bulkDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = false;
            } else {
                bulkDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = true;
            }
        }

        selectAllCheckbox.addEventListener('change', function() {
            bookingCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButtonState();
        });

        bookingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                }
                updateBulkDeleteButtonState();
            });
        });

        // Initial state update
        updateBulkDeleteButtonState();
    });
</script>

<?php include __DIR__ . '/../footer.php'; ?>