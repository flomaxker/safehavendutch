<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $creditAmount = (int)($_POST['credit_amount'] ?? 0);
    $priceCents = (int)($_POST['price_cents'] ?? 0);
    $active = isset($_POST['active']);
    $packageModel->update($id, $name, $description, $creditAmount, $priceCents, $active);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Package - Admin</title>
</head>
<body>
    <h1>Edit Package</h1>
    <form method="post">
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($package['name']) ?>" required></label><br>
        <label>Description:<br><textarea name="description" rows="4"><?= htmlspecialchars($package['description']) ?></textarea></label><br>
        <label>Credit Amount: <input type="number" name="credit_amount" value="<?= htmlspecialchars($package['credit_amount']) ?>" required></label><br>
        <label>Price (in cents): <input type="number" name="price_cents" value="<?= htmlspecialchars($package['price_cents']) ?>" required></label><br>
        <label><input type="checkbox" name="active" <?= $package['active'] ? 'checked' : '' ?>> Active</label><br>
        <button type="submit">Update Package</button>
    </form>
    <a href="index.php">Back to list</a>
</body>
</html>