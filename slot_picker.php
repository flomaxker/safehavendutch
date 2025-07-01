<?php
require_once __DIR__ . '/bootstrap.php';

use App\Models\Lesson;
use App\Models\User;

$lessonModel = $container->getLessonModel();
$userModel = $container->getUserModel();

$lessons = $lessonModel->getAll();

// Fetch teacher names
$teachers = [];
foreach ($lessons as $lesson) {
    if (!isset($teachers[$lesson['teacher_id']])) {
        $teacher = $userModel->find($lesson['teacher_id']);
        if ($teacher) {
            $teachers[$lesson['teacher_id']] = $teacher['name'];
        }
    }
}

$page_title = 'Select a Lesson Slot';

include __DIR__ . '/header.php';
?>

<div class="container">
    <h1>Select a Lesson Slot</h1>

    <div class="lesson-slots">
        <?php if (!empty($lessons)): ?>
            <?php foreach ($lessons as $lesson): ?>
                <div class="lesson-card">
                    <h2><?= htmlspecialchars($lesson['title']) ?></h2>
                    <p><strong>Teacher:</strong> <?= htmlspecialchars($teachers[$lesson['teacher_id']] ?? 'Unknown') ?></p>
                    <p><strong>Time:</strong> <?= htmlspecialchars($lesson['start_time']) ?> - <?= htmlspecialchars($lesson['end_time']) ?></p>
                    <p><strong>Capacity:</strong> <?= htmlspecialchars($lesson['capacity']) ?></p>
                    <p><?= htmlspecialchars($lesson['description']) ?></p>
                    <button class="btn btn-primary">Book Now</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No lessons available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
