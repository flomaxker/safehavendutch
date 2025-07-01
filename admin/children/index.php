<?php
require_once __DIR__ . '/../../bootstrap.php';

// TODO: Add authentication and authorization check

$childModel = $container->getChildModel();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && isset($_POST['selected_children']) && is_array($_POST['selected_children'])) {
        $ids = array_map('intval', $_POST['selected_children']);
        try {
            if ($childModel->deleteMany($ids)) {
                $message = count($ids) . ' children deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete selected children.';
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

$children = $childModel->getAll($order_by, $order_direction);

$page_title = 'Manage Children';

include __DIR__ . '/../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Children</h1>
        <p class="text-gray-500">View and manage all children associated with users.</p>
    </div>
    <a href="create.php" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition font-medium">Add New Child</a>
</header>

<?php if ($message): ?>
    <div class="mb-4 p-3 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?= $message ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
    <form method="post" id="bulkActionsForm">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <input type="checkbox" id="selectAllChildren" class="form-checkbox h-4 w-4 text-blue-600">
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
                        <a href="?order_by=user_name&order_direction=<?= ($order_by === 'user_name' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Parent
                            <?php if ($order_by === 'user_name'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=date_of_birth&order_direction=<?= ($order_by === 'date_of_birth' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Date of Birth
                            <?php if ($order_by === 'date_of_birth'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Notes
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($children)): ?>
                    <?php foreach ($children as $child): ?>
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <input type="checkbox" name="selected_children[]" value="<?= $child['id'] ?>" class="child-checkbox form-checkbox h-4 w-4 text-blue-600">
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($child['id']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($child['name']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($child['user_name']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($child['date_of_birth']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($child['notes']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="edit.php?id=<?= $child['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this child?');" class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            No children found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="px-5 py-3 bg-gray-100 border-t border-gray-200 flex justify-end">
            <button type="submit" name="action" value="bulk_delete" id="bulkDeleteBtn" class="text-red-600 border border-red-600 py-1 px-2 rounded-md hover:bg-red-50 transition text-xs opacity-50 cursor-not-allowed" disabled onclick="return confirm('Are you sure you want to delete selected children?');">Bulk Delete Selected</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllChildren');
        const childCheckboxes = document.querySelectorAll('.child-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        function updateBulkDeleteButtonState() {
            const anyChecked = Array.from(childCheckboxes).some(checkbox => checkbox.checked);
            if (anyChecked) {
                bulkDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = false;
            } else {
                bulkDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = true;
            }
        }

        selectAllCheckbox.addEventListener('change', function() {
            childCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButtonState();
        });

        childCheckboxes.forEach(checkbox => {
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
