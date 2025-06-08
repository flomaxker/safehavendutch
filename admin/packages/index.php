<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database;
use App\Models\Package;

$db = new Database();
$pdo = $db->getConnection();
$packageModel = new Package($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    if ($_POST['action'] === 'delete') {
        $packageModel->delete($id);
    } elseif ($_POST['action'] === 'toggle' && isset($_POST['active'])) {
        $active = $_POST['active'] === '1';
        $packageModel->toggleActive($id, $active);
    }
    header('Location: index.php');
    exit;
}

$packages = $packageModel->getAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Packages - Admin</title>
</head>
<body>
    <h1>Lesson Packages</h1>
    <a href="create.php">Add New Package</a>
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
</body>
</html>