<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;

$userModel = new User($container->getPdo());

$message = '';
$messageType = '';
$searchedUser = null;
$email = '';

// Step 2: Handle the final anonymization confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'anonymize' && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];
    if ($userModel->anonymize($userId)) {
        $message = "User data has been successfully anonymized.";
        $messageType = 'success';
    } else {
        $message = "An error occurred during anonymization.";
        $messageType = 'error';
    }
}
// Step 1: Handle the user search
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search') {
    $email = trim($_POST['email'] ?? '');
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $searchedUser = $userModel->findByEmail($email);
        if (!$searchedUser) {
            $message = "No user found with that email address.";
            $messageType = 'error';
        }
    } else {
        $message = "Please enter a valid email address.";
        $messageType = 'error';
    }
}

$page_title = 'GDPR Data Anonymization';
include __DIR__ . '/../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">GDPR Data Anonymization Tool</h1>
        <p class="text-gray-500">Anonymize personal data to comply with GDPR requests.</p>
    </div>
</header>

<?php if ($message): ?>
    <div class="mb-6 p-4 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Find User by Email</h2>
    <p class="text-gray-600 mb-6">
        Enter the email address of the user whose data you wish to anonymize. This action is irreversible and will permanently remove all personal identifying information associated with the user, including their name, email, and their children's details.
    </p>
    
    <!-- Search Form -->
    <form method="post" class="mb-8">
        <input type="hidden" name="action" value="search">
        <div class="flex items-center">
            <input type="email" name="email" placeholder="user@example.com" value="<?= htmlspecialchars($email) ?>" required class="shadow-sm appearance-none border rounded-l-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-r-lg">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <?php if ($searchedUser): ?>
        <!-- Confirmation Step -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-red-600">Confirm Anonymization</h3>
            <p class="my-2">Please review the user details below before proceeding. This action cannot be undone.</p>
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p><strong>User ID:</strong> <?= htmlspecialchars($searchedUser['id']) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($searchedUser['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($searchedUser['email']) ?></p>
                <p><strong>Registered:</strong> <?= htmlspecialchars($searchedUser['created_at']) ?></p>
            </div>
            <form method="post" onsubmit="return confirm('Are you absolutely sure you want to anonymize this user? This action is permanent and cannot be reversed.');">
                <input type="hidden" name="action" value="anonymize">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($searchedUser['id']) ?>">
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-user-shield mr-2"></i>I understand, proceed with anonymization
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
