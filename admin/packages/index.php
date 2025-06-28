<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\Package;

$db = new Database();
$pdo = $db->getConnection();
$packageModel = new Package($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    try {
        if ($_POST['action'] === 'delete') {
            $packageModel->delete($id);
            $message = 'Package deleted successfully!';
            $messageType = 'success';
        } elseif ($_POST['action'] === 'toggle' && isset($_POST['active'])) {
            $active = $_POST['active'] === '1';
            $packageModel->toggleActive($id, $active);
            $message = 'Package status updated successfully!';
            $messageType = 'success';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
    // Redirect to clear POST data and display message
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
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
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
                                <form method="post" style="display:inline;" class="inline-block">
                                    <input type="hidden" name="id" value="<?= $package['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" onclick="return confirm('Are you sure?');" class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"><i class="fas fa-times"></i></button>
                                </form>
                                <form method="post" style="display:inline;" class="inline-block">
                                    <input type="hidden" name="id" value="<?= $package['id'] ?>">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="active" value="<?= $package['active'] ? '0' : '1' ?>">
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                        <?= $package['active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            No packages found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php require __DIR__ . '/../footer.php'; ?>
