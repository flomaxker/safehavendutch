<?php
$page_title = 'Select a Lesson Slot';
require_once __DIR__ . '/user_header.php';

use App\Models\Lesson;
use App\Models\User;
use App\Calendar\iCalParser;

$lessonModel = $container->getLessonModel();
$userModel = $container->getUserModel();
$icalParser = new iCalParser();

$teachers = $userModel->getTeachers();
$teacherAvailabilities = [];

foreach ($teachers as $teacher) {
    if (!empty($teacher['ical_url'])) {
        try {
            $icalContent = @file_get_contents($teacher['ical_url']);
            if ($icalContent === false) {
                throw new \Exception("Could not fetch iCal content from " . $teacher['ical_url']);
            }
            $parsedEvents = $icalParser->parseString($icalContent);
            foreach ($parsedEvents as &$event) {
                $event['teacher_name'] = $teacher['name'];
            }
            $teacherAvailabilities[$teacher['id']] = $parsedEvents;

        } catch (Exception $e) {
            error_log("Error parsing iCal for teacher " . $teacher['name'] . ": " . $e->getMessage());
        }
    }
}

$page_title = 'Select a Lesson Slot';


?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Select a Lesson Slot</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($teacherAvailabilities)): ?>
            <?php foreach ($teacherAvailabilities as $teacherId => $events): ?>
                <?php foreach ($events as $event): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($event['summary']) ?></h2>
                        <p class="text-gray-600 mb-1"><strong>Teacher:</strong> <?= htmlspecialchars($event['teacher_name']) ?></p>
                        <p class="text-gray-600"><strong>Time:</strong> <?= htmlspecialchars($event['dtstart']) ?> - <?= htmlspecialchars($event['dtend']) ?></p>
                        <form action="book_lesson.php" method="post">
                        <input type="hidden" name="summary" value="<?= htmlspecialchars($event['summary']) ?>">
                        <input type="hidden" name="dtstart" value="<?= htmlspecialchars($event['dtstart']) ?>">
                        <input type="hidden" name="dtend" value="<?= htmlspecialchars($event['dtend']) ?>">
                        <input type="hidden" name="teacher_id" value="<?= htmlspecialchars($teacherId) ?>">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-500 transition mt-4">Book Now</button>
                    </form>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600">No teacher availabilities found.</p>
        <?php endif; ?>
    </div>
</div>


