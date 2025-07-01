<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\User;

$db = new Database();
$pdo = $db->getConnection();
$userModel = new User($pdo);

$message = '';
$messageType = '';

// Initialize sorting parameters
$order_by = $_GET['order_by'] ?? 'id';
$order_direction = $_GET['order_direction'] ?? 'ASC';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        try {
            if ($userModel->delete($id)) {
                $message = 'User deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete user.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && isset($_POST['selected_users']) && is_array($_POST['selected_users'])) {
        $ids = array_map('intval', $_POST['selected_users']);
        try {
            if ($userModel->deleteMany($ids)) {
                $message = count($ids) . ' users deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete selected users.';
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

$users = $userModel->getAll($order_by, $order_direction);

include __DIR__ . '/../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
        <p class="text-gray-500">View and manage all registered users.</p>
    </div>
</header>

<?php if ($message): ?>
    <div class="mb-4 p-3 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?= $message ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
    <form method="post" id="bulkDeleteForm">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <input type="checkbox" id="selectAllUsers" class="form-checkbox h-4 w-4 text-blue-600">
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
                        <a href="?order_by=name&order_direction=<?= ($order_by === 'name' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Name
                            <?php if ($order_by === 'name'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=email&order_direction=<?= ($order_by === 'email' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Email
                            <?php if ($order_by === 'email'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=euro_balance&order_direction=<?= ($order_by === 'euro_balance' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Euro Balance
                            <?php if ($order_by === 'euro_balance'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=role&order_direction=<?= ($order_by === 'role' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Role
                            <?php if ($order_by === 'role'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=created_at&order_direction=<?= ($order_by === 'created_at' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Registered At
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
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <input type="checkbox" name="selected_users[]" value="<?= $user['id'] ?>" class="user-checkbox form-checkbox h-4 w-4 text-blue-600">
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php echo htmlspecialchars($user['id']); ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php echo htmlspecialchars($user['name']); ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                €<?= number_format(($user['euro_balance'] ?? 0) / 100, 2) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php echo htmlspecialchars($user['role'] ?? ''); ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php echo htmlspecialchars($user['created_at']); ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="edit.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this user?');" class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            No users found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="px-5 py-3 bg-gray-100 border-t border-gray-200 flex justify-end">
            <button type="submit" name="action" value="bulk_delete" id="bulkDeleteBtn" class="text-red-600 border border-red-600 py-1 px-2 rounded-md hover:bg-red-50 transition text-xs opacity-50 cursor-not-allowed" disabled onclick="return confirm('Are you sure you want to delete selected users?');">Bulk Delete Selected</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllUsers');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        function updateBulkDeleteButtonState() {
            const anyChecked = Array.from(userCheckboxes).some(checkbox => checkbox.checked);
            if (anyChecked) {
                bulkDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = false;
            } else {
                bulkDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = true;
            }
        }

        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButtonState();
        });

        userCheckboxes.forEach(checkbox => {
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
