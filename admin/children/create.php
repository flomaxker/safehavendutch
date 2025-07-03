<?php
$page_title = "Add New Child";
require_once __DIR__ . '/../header.php';

use App
Models\Child;
use App
Models\User;

$childModel = $container->getChildModel();
$userModel = $container->getUserModel();

$users = $userModel->getAll(); // Get all users to assign a child to

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)$_POST['user_id'];
    $name = trim($_POST['name']);
    $dateOfBirth = trim($_POST['date_of_birth']);
    $notes = trim($_POST['notes']);

    if (empty($name)) {
        $_SESSION['error_message'] = 'Child\'s name cannot be empty.';
    } elseif (empty($userId)) {
        $_SESSION['error_message'] = 'Please select a parent for the child.';
    } else {
        $data = [
            'user_id' => $userId,
            'name' => $name,
            'date_of_birth' => $dateOfBirth,
            'notes' => $notes,
        ];

        if ($childModel->create($data)) {
            $_SESSION['success_message'] = 'Child added successfully.';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Failed to add child.';
        }
    }
}

?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Child</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['success_message']); ?></span>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['error_message']); ?></span>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <form action="create.php" method="post">
            <div class="mb-4">
                <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Parent:</label>
                <select id="user_id" name="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select a parent</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Child's Name:</label>
                <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="date_of_birth" class="block text-gray-700 text-sm font-bold mb-2">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes:</label>
                <textarea id="notes" name="notes" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-500 transition">Add Child</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>