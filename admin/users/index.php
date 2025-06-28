<?php

require_once __DIR__ . '/../../bootstrap.php';
include __DIR__ . '/../header.php';

use App\Database\Database;
use App\Models\User;

$db = new Database();
$pdo = $db->getConnection();
$userModel = new User($pdo);

$users = $userModel->getAll();

?>

<div class="container">
    <h2>User Management</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Credit Balance</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['credit_balance']); ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
