<?php
require_once __DIR__ . '/../../bootstrap.php';

// TODO: Add authentication and authorization check

$childModel = $container->getChildModel();
$userModel = $container->getUserModel(); // Assuming you have a UserModel in your container
$users = $userModel->getAll(); // Get all users to populate the dropdown

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $childModel->create($_POST);
    header('Location: index.php');
    exit;
}

$page_title = 'Create Child';

include __DIR__ . '/../header.php';
?>

<div class="container">
    <h1>Create Child</h1>
    <form method="post">
        <div class="form-group">
            <label for="user_id">Parent</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control">
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
