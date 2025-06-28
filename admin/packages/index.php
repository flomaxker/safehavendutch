<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\Package;

$db = new Database();
$pdo = $db->getConnection();
$packageModel = new Package($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        try {
            if ($packageModel->delete($id)) {
                $message = 'Package deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete package.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'toggle' && isset($_POST['id']) && isset($_POST['active'])) {
        $id = (int)$_POST['id'];
        $active = $_POST['active'] === '1';
        try {
            if ($packageModel->toggleActive($id, $active)) {
                $message = 'Package status updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to update package status.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && isset($_POST['selected_packages']) && is_array($_POST['selected_packages'])) {
        $ids = array_map('intval', $_POST['selected_packages']);
        try {
            if ($packageModel->deleteMany($ids)) {
                $message = count($ids) . ' packages deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete selected packages.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'bulk_deactivate' && isset($_POST['selected_packages']) && is_array($_POST['selected_packages'])) {
        $ids = array_map('intval', $_POST['selected_packages']);
        try {
            if ($packageModel->toggleActiveMany($ids, false)) {
                $message = count($ids) . ' packages deactivated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to deactivate selected packages.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'bulk_activate' && isset($_POST['selected_packages']) && is_array($_POST['selected_packages'])) {
        $ids = array_map('intval', $_POST['selected_packages']);
        try {
            if ($packageModel->toggleActiveMany($ids, true)) {
                $message = count($ids) . ' packages activated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to activate selected packages.';
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

$packages = $packageModel->getAll();
$title = 'Packages - Admin';
require __DIR__ . '/../header.php';
?>
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Lesson Packages</h1>
            <p class="text-gray-500">Manage all available lesson packages.</p>
        </div>
        <a href="create.php" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition font-medium">Add New Package</a>
    </header>

    <?php if ($message): ?>
        <div class="mb-4 p-3 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form method="post" id="bulkActionsForm">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllPackages" class="form-checkbox h-4 w-4 text-blue-600">
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Credits
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Price (cents)
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Active
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($packages)): ?>
                        <?php foreach ($packages as $package): ?>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <input type="checkbox" name="selected_packages[]" value="<?= $package['id'] ?>" class="package-checkbox form-checkbox h-4 w-4 text-blue-600">
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?= htmlspecialchars($package['id']) ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?= htmlspecialchars($package['name']) ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?= htmlspecialchars($package['credit_amount']) ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?= htmlspecialchars($package['price_cents']) ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?= $package['active'] ? 'Yes' : 'No' ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <a href="edit.php?id=<?= $package['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this package?');" class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"><i class="fas fa-times"></i></button>
                                    <button type="submit" name="action" value="toggle" onclick="return confirm('Are you sure you want to toggle the status of this package?');" class="text-indigo-600 hover:text-indigo-900 p-2 rounded-full hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"><i class="fas fa-toggle-on"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                No packages found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="px-5 py-3 bg-gray-100 border-t border-gray-200 flex justify-end space-x-2">
                <button type="submit" name="action" value="bulk_activate" id="bulkActivateBtn" class="text-green-600 border border-green-600 py-1 px-2 rounded-md hover:bg-green-50 transition text-xs opacity-50 cursor-not-allowed" disabled onclick="return confirm('Are you sure you want to activate selected packages?');">Bulk Activate</button>
                <button type="submit" name="action" value="bulk_deactivate" id="bulkDeactivateBtn" class="text-yellow-600 border border-yellow-600 py-1 px-2 rounded-md hover:bg-yellow-50 transition text-xs opacity-50 cursor-not-allowed" disabled onclick="return confirm('Are you sure you want to deactivate selected packages?');">Bulk Deactivate</button>
                <button type="submit" name="action" value="bulk_delete" id="bulkDeleteBtn" class="text-red-600 border border-red-600 py-1 px-2 rounded-md hover:bg-red-50 transition text-xs opacity-50 cursor-not-allowed" disabled onclick="return confirm('Are you sure you want to delete selected packages?');">Bulk Delete</button>
            </div>
        </form>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllPackages');
        const packageCheckboxes = document.querySelectorAll('.package-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeactivateBtn = document.getElementById('bulkDeactivateBtn');
        const bulkActivateBtn = document.getElementById('bulkActivateBtn');

        function updateBulkActionButtonsState() {
            const anyChecked = Array.from(packageCheckboxes).some(checkbox => checkbox.checked);
            [bulkDeleteBtn, bulkDeactivateBtn, bulkActivateBtn].forEach(btn => {
                if (anyChecked) {
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    btn.disabled = false;
                } else {
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    btn.disabled = true;
                }
            });
        }

        selectAllCheckbox.addEventListener('change', function() {
            packageCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkActionButtonsState();
        });

        packageCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                }
                updateBulkActionButtonsState();
            });
        });

        // Initial state update
        updateBulkActionButtonsState();
    });
</script>

<?php require __DIR__ . '/../footer.php'; ?>
