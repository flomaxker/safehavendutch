<?php
declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

// Admin-only access guard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$pdo = $container->getPdo();

// Validate and fetch child by id from GET
$child_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($child_id <= 0) {
    $_SESSION['error_message'] = 'Invalid child ID.';
    header('Location: /admin/children/index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM children WHERE id = :id');
$stmt->execute([':id' => $child_id]);
$child = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$child) {
    $_SESSION['error_message'] = 'Child not found.';
    header('Location: /admin/children/index.php');
    exit;
}

// Fetch parent for context
$parent_stmt = $pdo->prepare('SELECT name, email FROM users WHERE id = :id');
$parent_stmt->execute([':id' => (int) $child['user_id']]);
$parent = $parent_stmt->fetch(PDO::FETCH_ASSOC) ?: ['name' => 'Unknown', 'email' => ''];

// Prepare page
$page_title = 'Edit Child';

// Prepare CSRF token for this form
$_SESSION['csrf_token'] = $nonce;

// Flash messages and old input
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
$form_errors = $_SESSION['form_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];
$use_old_input = ($error_message !== null) || !empty($form_errors);

// Compute safe, pre-populated field values
$name_value = (string) ($use_old_input ? ($old_input['name'] ?? '') : ($child['name'] ?? ''));

$dob_value = '';
if ($use_old_input) {
    $candidate = (string) ($old_input['date_of_birth'] ?? '');
    if ($candidate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $candidate)) {
        $dob_value = $candidate; // assume already in ISO format
    }
} else {
    $candidate = (string) ($child['date_of_birth'] ?? '');
    if ($candidate !== '') {
        try {
            $dt = new DateTime($candidate);
            $dob_value = $dt->format('Y-m-d');
        } catch (Throwable $e) {
            $dob_value = '';
        }
    }
}

$notes_value = (string) ($use_old_input ? ($old_input['notes'] ?? '') : ($child['notes'] ?? ''));
unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['form_errors'], $_SESSION['old_input']);

require_once __DIR__ . '/../header.php';
?>

<div class="px-4 py-8 max-w-2xl mx-auto md:mx-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Child Profile: <?= htmlspecialchars((string)($child['name'] ?? '')); ?></h1>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($success_message); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message || !empty($form_errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <?php if ($error_message): ?><div class="font-medium mb-1"><?= htmlspecialchars($error_message); ?></div><?php endif; ?>
            <?php if (!empty($form_errors)): ?>
                <ul class="list-disc pl-5">
                    <?php foreach ($form_errors as $err): ?>
                        <li><?= htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-2xl shadow">
        <div class="mb-4 text-sm text-gray-600">
            <div class="font-semibold">Parent</div>
            <div><?= htmlspecialchars($parent['name']); ?><?= $parent['email'] ? ' (' . htmlspecialchars($parent['email']) . ')' : ''; ?></div>
        </div>

        <form action="update_handler.php" method="post" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <input type="hidden" name="child_id" value="<?= (int) $child_id; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Child's Name</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($name_value); ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="date_of_birth" class="block text-gray-700 text-sm font-semibold mb-2">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required value="<?= htmlspecialchars($dob_value); ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            <div>
                <label for="notes" class="block text-gray-700 text-sm font-semibold mb-2">Notes (optional)</label>
                <textarea id="notes" name="notes" rows="4" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($notes_value); ?></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Save Changes</button>
                <a href="/admin/children/index.php" class="text-gray-700 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
