<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\Package;

$db = new Database();
$pdo = $db->getConnection();
$packageModel = new Package($pdo);

$errors = [];
$success = '';

$name = '';
$description = '';
$creditAmount = 0;
$priceCents = 0;
$active = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $creditAmount = (int)($_POST['credit_amount'] ?? 0);
    $priceEuros = (float)($_POST['price_euros'] ?? 0.0);
    $priceCents = (int)($priceEuros * 100);
    $active = isset($_POST['active']);

    if (empty($name)) {
        $errors[] = 'Package name is required.';
    }
    if ($creditAmount <= 0) {
        $errors[] = 'Credit amount must be a positive number.';
    }
    if ($priceCents < 0) {
        $errors[] = 'Price (in Euros) cannot be negative.';
    }

    if (empty($errors)) {
        $packageModel->create($name, $description, $creditAmount, $priceCents, $active);
        $success = 'Package created successfully!';
        // Clear form fields after successful submission
        $name = '';
        $description = '';
        $creditAmount = 0;
        $priceEuros = 0.00;
        $active = true;
    }
}
$title = 'Add Package - Admin';
require __DIR__ . '/../header.php';
?>
<div class="px-4 py-8 max-w-2xl mx-auto md:mx-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Package</h1>

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

    <form method="post" class="space-y-4 bg-white p-6 rounded-2xl shadow">
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required></label><br>
        <label>Description:<br><textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea></label><br>
        <label>Credit Amount: <input type="number" name="credit_amount" value="<?= htmlspecialchars($creditAmount) ?>" required></label><br>
        <label>Price (in Euros): <input type="number" name="price_euros" value="<?= htmlspecialchars(number_format($priceEuros, 2)) ?>" step="0.01" required></label><br>
        <label><input type="checkbox" name="active" <?= $active ? 'checked' : '' ?>> Active</label><br>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Create Package</button>
    </form>
    <a class="inline-block mt-4 text-blue-600 hover:underline" href="index.php">Back to list</a>
</div>
<?php require __DIR__ . '/../footer.php'; ?>
