<?php
declare(strict_types=1);

$page_title = 'Add New Child';
require_once __DIR__ . '/../header.php';

// Ensure CSRF token is set for this form (reuse nonce like other admin pages)
$_SESSION['csrf_token'] = $nonce;

$pdo = $container->getPdo();

// Fetch only parents (treat 'member' as parent; include 'parent' for forward compatibility)
$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE role IN ('member','parent') ORDER BY name ASC");
$stmt->execute();
$parents = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Load flash data
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
$form_errors = $_SESSION['form_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];
unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['form_errors'], $_SESSION['old_input']);
?>

<div class="px-4 py-8 max-w-2xl mx-auto md:mx-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Child</h1>

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
        <form action="create_handler.php" method="post" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <div>
                <label for="user_id" class="block text-gray-700 text-sm font-semibold mb-2">Parent</label>
                <select id="user_id" name="user_id" required class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a parent</option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?= htmlspecialchars((string)$parent['id']); ?>" <?= isset($old_input['user_id']) && (string)$old_input['user_id'] === (string)$parent['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($parent['name']); ?> (<?= htmlspecialchars($parent['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Child's Name</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($old_input['name'] ?? ''); ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label for="date_of_birth" class="block text-gray-700 text-sm font-semibold mb-2">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($old_input['date_of_birth'] ?? ''); ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            <div>
                <label for="notes" class="block text-gray-700 text-sm font-semibold mb-2">Notes (optional)</label>
                <textarea id="notes" name="notes" rows="4" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($old_input['notes'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Add Child</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../footer.php'; ?>
