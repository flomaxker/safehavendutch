<?php
// CLI seeder for demo lessons and bookings
// Usage: php scripts/seed-lessons-and-bookings.php

declare(strict_types=1);

define('PROJECT_ROOT', dirname(__DIR__));

// Autoload (Composer or PSR-4 fallback)
if (file_exists(PROJECT_ROOT . '/vendor/autoload.php')) {
    require_once PROJECT_ROOT . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = PROJECT_ROOT . '/app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $path = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($path)) {
                require_once $path;
            }
        }
    });
}

// Load .env
if (file_exists(PROJECT_ROOT . '/.env')) {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(PROJECT_ROOT);
    $dotenv->load();
}

use App\Database\Database;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Booking;

// Connect DB
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (\PDOException $e) {
    fwrite(STDERR, 'Database connection failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

$userModel = new User($pdo);
$lessonModel = new Lesson($pdo);
$bookingModel = new Booking($pdo);

// Ensure a demo teacher exists (role stays 'member' for current enum)
$teacherEmail = 'teacher.demo@safehaven.com';
$teacher = $userModel->findByEmail($teacherEmail);
if (!$teacher) {
    $teacherId = $userModel->create(
        'Teacher Demo',
        $teacherEmail,
        password_hash('password', PASSWORD_DEFAULT),
        'member',
        0,
        '[]',
        'https://example.com/teacher_demo.ics'
    );
    $teacher = $userModel->find($teacherId);
    echo "Created demo teacher: {$teacherEmail} (ID {$teacherId})\n";
} else {
    echo "Found demo teacher: {$teacherEmail} (ID {$teacher['id']})\n";
}

// Determine batch tag to avoid duplicate lesson seeding
$batchTag = date('Ymd');

// Check if this batch already exists
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE title LIKE :t");
$checkStmt->execute([':t' => "Demo Lesson ({$batchTag}) %"]);
$existingCount = (int) $checkStmt->fetchColumn();
if ($existingCount > 0) {
    echo "Demo lessons for batch {$batchTag} already exist. Skipping lesson creation.\n";
} else {
    // Create a handful of upcoming lessons
    $now = new DateTime();
    $lessonsToCreate = [];
    for ($i = 1; $i <= 6; $i++) {
        $start = (clone $now)->modify("+{$i} days")->setTime(10 + ($i % 3) * 2, 0); // 10:00, 12:00, 14:00
        $end = (clone $start)->modify('+1 hour');
        $lessonsToCreate[] = [
            'title' => sprintf('Demo Lesson (%s) #%d', $batchTag, $i),
            'description' => 'Automatically seeded demo lesson.',
            'teacher_id' => (int) $teacher['id'],
            'start_time' => $start->format('Y-m-d H:i:s'),
            'end_time' => $end->format('Y-m-d H:i:s'),
            'capacity' => 3,
            'credit_cost' => 100, // cost in euro credits
        ];
    }

    foreach ($lessonsToCreate as $lesson) {
        $ok = $lessonModel->create($lesson);
        if ($ok) {
            echo "Created lesson: {$lesson['title']} ({$lesson['start_time']} - {$lesson['end_time']})\n";
        } else {
            echo "Failed to create lesson: {$lesson['title']}\n";
        }
    }
}

// Fetch lessons for this batch
$lessonsStmt = $pdo->prepare("SELECT * FROM lessons WHERE title LIKE :t ORDER BY start_time ASC");
$lessonsStmt->execute([':t' => "Demo Lesson ({$batchTag}) %"]);
$lessons = $lessonsStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($lessons)) {
    echo "No demo lessons found to book. Exiting.\n";
    exit(0);
}

// Get children with their parents
$childrenStmt = $pdo->query("SELECT c.id AS child_id, c.user_id, c.name FROM children c");
$children = $childrenStmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($children)) {
    echo "No children found. Consider running php scripts/seed-database.php first.\n";
    exit(0);
}

// Shuffle to distribute randomly
shuffle($children);

// Book up to capacity per lesson for random children
foreach ($lessons as $lesson) {
    $lessonId = (int) $lesson['id'];
    $capacity = (int) $lesson['capacity'];
    $cost = (int) $lesson['credit_cost'];

    // Skip if already fully booked
    $currentCount = (new Lesson($pdo))->getBookingCount($lessonId);
    if ($currentCount >= $capacity) {
        echo "Lesson {$lessonId} already full ({$currentCount}/{$capacity}). Skipping.\n";
        continue;
    }

    $toBook = max(0, $capacity - $currentCount);
    $slice = array_slice($children, 0, $toBook);

    foreach ($slice as $child) {
        $userId = (int) $child['user_id'];
        // Ensure user has enough balance
        $balance = $userModel->getEuroBalance($userId);
        if ($balance < $cost) {
            $userModel->updateEuroBalance($userId, $cost - $balance);
        }

        try {
            $bookingId = $bookingModel->create([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'child_id' => (int) $child['child_id'],
                'status' => 'confirmed',
            ]);
            echo "Booked child {$child['name']} (User {$userId}) into lesson {$lesson['title']} (Booking ID {$bookingId})\n";
        } catch (\Exception $e) {
            echo "Failed to book child {$child['child_id']} into lesson {$lessonId}: {$e->getMessage()}\n";
        }
    }
}

echo "Seeding lessons and bookings complete.\n";

