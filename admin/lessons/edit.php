<?php
require_once __DIR__ . '/../../bootstrap.php';

// TODO: Add authentication and authorization check

$lessonModel = $container->getLessonModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lessonModel->update($_GET['id'], $_POST);
    header('Location: index.php');
    exit;
}

$lesson = $lessonModel->findById($_GET['id']);

$page_title = 'Edit Lesson';

include __DIR__ . '/../header.php';
?>

<div class="px-4 py-8 max-w-2xl mx-auto md:mx-0">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Lesson</h1>
    <form method="post" class="space-y-4 bg-white p-6 rounded-2xl shadow">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($lesson['title']) ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"><?= htmlspecialchars($lesson['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="teacher_id">Teacher</label>
            <input type="number" name="teacher_id" id="teacher_id" class="form-control" value="<?= htmlspecialchars($lesson['teacher_id']) ?>" required>
        </div>
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($lesson['start_time'])) ?>" required>
        </div>
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($lesson['end_time'])) ?>" required>
        </div>
        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" name="capacity" id="capacity" class="form-control" value="<?= htmlspecialchars($lesson['capacity']) ?>" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Update</button>
    </form>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
