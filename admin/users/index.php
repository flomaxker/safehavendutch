<?php
session_start();

// This is a protected area. User must be an admin to access it.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../../bootstrap.php';
include __DIR__ . '/../header.php';

$db = \App\Database\Database::getInstance();
$stmt = $db->query("SELECT id, name, email, credit_balance, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

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
