<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\Package;

$db = new Database();
$pdo = $db->getConnection();
$packageModel = new Package($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $creditAmount = (int)($_POST['credit_amount'] ?? 0);
    $priceCents = (int)($_POST['price_cents'] ?? 0);
    $active = isset($_POST['active']);
    $packageModel->create($name, $description, $creditAmount, $priceCents, $active);
    header('Location: index.php');
    exit;
}
$title = 'Add Package - Admin';
require __DIR__ . '/../header.php';
?>
    <h1>Add New Package</h1>
    <form method="post">
        <label>Name: <input type="text" name="name" required></label><br>
        <label>Description:<br><textarea name="description" rows="4"></textarea></label><br>
        <label>Credit Amount: <input type="number" name="credit_amount" required></label><br>
        <label>Price (in cents): <input type="number" name="price_cents" required></label><br>
        <label><input type="checkbox" name="active" checked> Active</label><br>
        <button type="submit">Create Package</button>
    </form>
    <a href="index.php">Back to list</a>
<?php require __DIR__ . '/../footer.php'; ?>
