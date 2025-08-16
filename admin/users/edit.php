<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Database\Database;
use App\Models\User;

$db = new Database();
$pdo = $db->getConnection();
$userModel = new User($pdo);

$id = (int)($_GET['id'] ?? 0);
$user = $userModel->findByEmail($id); // Assuming findByEmail can also take ID for simplicity or add getById

// If user not found, try by ID if findByEmail doesn't work with ID
if (!$user) {
    $user = $userModel->getById($id); // Assuming getById exists or will be added
}

if (!$user) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = '';

// Initialize form fields with existing user data
$name = $user['name'];
$email = $user['email'];
$creditBalance = $user['credit_balance'];
$role = $user['role'];
$icalUrl = $user['ical_url'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $euroBalance = (int)($_POST['euro_balance'] ?? 0);
    $role = trim($_POST['role'] ?? '');

    if (empty($name)) {
        $errors[] = 'User name is required.';
    }
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if ($euroBalance < 0) {
        $errors[] = 'Euro balance cannot be negative.';
    }
    if (!in_array($role, ['admin', 'member'])) {
        $errors[] = 'Invalid user role.';
    }

    if (empty($errors)) {
        $icalUrl = trim($_POST['ical_url'] ?? '');
        $userModel->update($id, $name, $email, $euroBalance, $role, $user['quick_actions_order'], $icalUrl);
        $success = 'User updated successfully!';
        // Re-fetch user data to reflect changes
        $user = $userModel->find($id); // Use find by ID
        $name = $user['name'];
        $email = $user['email'];
        $euroBalance = $user['euro_balance'];
        $role = $user['role'];
        $icalUrl = $user['ical_url'] ?? '';
    }
}

$title = 'Edit User - Admin';
require __DIR__ . '/../header.php';
?>
<div class="px-4 py-8 max-w-2xl mx-auto md:mx-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit User</h1>

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
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required></label><br>
        <label>Euro Balance: <input type="number" name="euro_balance" value="<?= htmlspecialchars($euroBalance) ?>" required></label><br>
        <label>Role:
            <select name="role" required>
                <option value="member" <?= ($role === 'member') ? 'selected' : '' ?>>Member</option>
                <option value="admin" <?= ($role === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
        </label><br>
        <label>iCal URL: <input type="url" name="ical_url" value="<?= htmlspecialchars($icalUrl) ?>"></label><br>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Update User</button>
    </form>
    <a class="inline-block mt-4 text-blue-600 hover:underline" href="index.php">Back to list</a>
</div>
<?php require __DIR__ . '/../footer.php'; ?>
