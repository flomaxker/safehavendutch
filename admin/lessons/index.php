<?php
require_once __DIR__ . '/../../bootstrap.php';

// TODO: Add authentication and authorization check

$lessonModel = $container->getLessonModel();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && isset($_POST['selected_lessons']) && is_array($_POST['selected_lessons'])) {
        $ids = array_map('intval', $_POST['selected_lessons']);
        try {
            if ($lessonModel->deleteMany($ids)) {
                $message = count($ids) . ' lessons deleted successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to delete selected lessons.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    header('Location: index.php?message=' . urlencode($message) . '&type=' . urlencode($messageType));
    exit;
}

// Check for messages from redirect
if (isset($_GET['message'], $_GET['type'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = htmlspecialchars($_GET['type']);
}

// Initialize sorting parameters
$order_by = $_GET['order_by'] ?? 'id';
$order_direction = $_GET['order_direction'] ?? 'ASC';

$lessons = $lessonModel->getAll($order_by, $order_direction);

$page_title = 'Manage Lessons';

include __DIR__ . '/../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manage Lessons</h1>
        <p class="text-gray-500">View and manage all lessons.</p>
    </div>
    <a href="create.php" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition font-medium">Add New Lesson</a>
</header>

<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
    <form method="post" id="bulkActionsForm">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <input type="checkbox" id="selectAllLessons" class="form-checkbox h-4 w-4 text-blue-600">
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=id&order_direction=<?= ($order_by === 'id' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            ID
                            <?php if ($order_by === 'id'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=title&order_direction=<?= ($order_by === 'title' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Title
                            <?php if ($order_by === 'title'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=teacher_id&order_direction=<?= ($order_by === 'teacher_id' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Teacher
                            <?php if ($order_by === 'teacher_id'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=start_time&order_direction=<?= ($order_by === 'start_time' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Start Time
                            <?php if ($order_by === 'start_time'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=end_time&order_direction=<?= ($order_by === 'end_time' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            End Time
                            <?php if ($order_by === 'end_time'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <a href="?order_by=capacity&order_direction=<?= ($order_by === 'capacity' && $order_direction === 'ASC') ? 'DESC' : 'ASC' ?>" class="flex items-center">
                            Capacity
                            <?php if ($order_by === 'capacity'): ?>
                                <i class="fas fa-sort-<?= ($order_direction === 'ASC') ? 'up' : 'down' ?> ml-1"></i>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lessons)): ?>
                    <?php foreach ($lessons as $lesson): ?>
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <input type="checkbox" name="selected_lessons[]" value="<?= $lesson['id'] ?>" class="lesson-checkbox form-checkbox h-4 w-4 text-blue-600">
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($lesson['id']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($lesson['title']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($lesson['teacher_id']) // TODO: Get teacher name from user ID ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($lesson['start_time']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($lesson['end_time']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?= htmlspecialchars($lesson['capacity']) ?>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="edit.php?id=<?= $lesson['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <a href="delete.php?id=<?= $lesson['id'] ?>" class="text-red-600 hover:text-red-900">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            No lessons found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="px-5 py-3 bg-gray-100 border-t border-gray-200 flex justify-end">
            <button type="submit" name="action" value="bulk_delete" id="bulkDeleteBtn" class="text-red-600 border border-red-600 py-1 px-2 rounded-md hover:bg-red-50 transition text-xs opacity-50 cursor-not-allowed" disabled onclick="return confirm('Are you sure you want to delete selected lessons?');">Bulk Delete Selected</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllLessons');
        const lessonCheckboxes = document.querySelectorAll('.lesson-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        function updateBulkDeleteButtonState() {
            const anyChecked = Array.from(lessonCheckboxes).some(checkbox => checkbox.checked);
            if (anyChecked) {
                bulkDeleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = false;
            } else {
                bulkDeleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                bulkDeleteBtn.disabled = true;
            }
        }

        selectAllCheckbox.addEventListener('change', function() {
            lessonCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButtonState();
        });

        lessonCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                }
                updateBulkDeleteButtonState();
            });
        });

        // Initial state update
        updateBulkDeleteButtonState();
    });
</script>

<?php include __DIR__ . '/../footer.php'; ?>