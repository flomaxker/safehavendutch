<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\Package;

$db = new Database();
$pdo = $db->getConnection();
$packageModel = new Package($pdo);

$id = (int)($_GET['id'] ?? 0);
$package = $packageModel->getById($id);

if (!$package) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = '';

// Initialize form fields with existing package data
$name = $package['name'];
$description = $package['description'];
$creditAmount = $package['credit_amount'];
$priceCents = $package['price_cents'];
$active = (bool)$package['active'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $creditAmount = (int)($_POST['credit_amount'] ?? 0);
    $priceCents = (int)($_POST['price_cents'] ?? 0);
    $active = isset($_POST['active']);

    if (empty($name)) {
        $errors[] = 'Package name is required.';
    }
    if ($creditAmount <= 0) {
        $errors[] = 'Credit amount must be a positive number.';
    }
    if ($priceCents < 0) {
        $errors[] = 'Price (in cents) cannot be negative.';
    }

    if (empty($errors)) {
        $packageModel->update($id, $name, $description, $creditAmount, $priceCents, $active);
        $success = 'Package updated successfully!';
        // Re-fetch package data to reflect changes, especially if active status was toggled
        $package = $packageModel->getById($id);
        $name = $package['name'];
        $description = $package['description'];
        $creditAmount = $package['credit_amount'];
        $priceCents = $package['price_cents'];
        $active = (bool)$package['active'];
    }
}

$title = 'Edit Package - Admin';
require __DIR__ . '/../header.php';
?>
    <h1>Edit Package</h1>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color: green;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required></label><br>
        <label>Description:<br><textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea></label><br>
        <label>Credit Amount: <input type="number" name="credit_amount" value="<?= htmlspecialchars($creditAmount) ?>" required></label><br>
        <label>Price (in cents): <input type="number" name="price_cents" value="<?= htmlspecialchars($priceCents) ?>" required></label><br>
        <label><input type="checkbox" name="active" <?= $active ? 'checked' : '' ?>> Active</label><br>
        <button type="submit">Update Package</button>
    </form>
    <a href="index.php">Back to list</a>
<?php require __DIR__ . '/../footer.php'; ?>
