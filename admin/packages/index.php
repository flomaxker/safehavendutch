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
    <h1>Lesson Packages</h1>
    <a href="create.php">Add New Package</a>

    <?php if ($message): ?>
        <div style="color: <?= $messageType === 'success' ? 'green' : 'red' ?>;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Credits</th>
                <th>Price (cents)</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($packages as $package): ?>
            <tr>
                <td><?= htmlspecialchars($package['id']) ?></td>
                <td><?= htmlspecialchars($package['name']) ?></td>
                <td><?= htmlspecialchars($package['credit_amount']) ?></td>
                <td><?= htmlspecialchars($package['price_cents']) ?></td>
                <td><?= $package['active'] ? 'Yes' : 'No' ?></td>
                <td>
                    <a href="edit.php?id=<?= $package['id'] ?>">Edit</a>
                    <form method="post" style="display:inline">
                        <input type="hidden" name="id" value="<?= $package['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                    </form>
                    <form method="post" style="display:inline">
                        <input type="hidden" name="id" value="<?= $package['id'] ?>">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="active" value="<?= $package['active'] ? '0' : '1' ?>">
                        <button type="submit"><?= $package['active'] ? 'Deactivate' : 'Activate' ?></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php require __DIR__ . '/../footer.php'; ?>
