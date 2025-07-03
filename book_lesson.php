<?php

require_once __DIR__ . '/bootstrap.php';

use App\Database\Database;
use App\Models\Booking;
use App\Models\Lesson;
use App\Calendar\iCalGenerator;
use App\Mail\Mailer;
use App\Models\User;

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();
$bookingModel = new Booking($pdo);
$lessonModel = new Lesson($pdo);
$icalGenerator = new iCalGenerator();
$mailer = new Mailer();
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $summary = $_POST['summary'] ?? null;
    $dtstart = $_POST['dtstart'] ?? null;
    $dtend = $_POST['dtend'] ?? null;
    $teacherId = $_POST['teacher_id'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$summary || !$dtstart || !$dtend || !$teacherId) {
        $_SESSION['error_message'] = 'Missing lesson details.';
        header('Location: slot_picker.php');
        exit;
    }

    // Convert iCal format to DateTime for database
    $startTime = date('Y-m-d H:i:s', strtotime($dtstart));
    $endTime = date('Y-m-d H:i:s', strtotime($dtend));

    // Find or create the lesson in the database
    $lesson = $lessonModel->findOrCreate(
        $summary,
        'Booking from iCal', // Default description
        $teacherId,
        $startTime,
        $endTime,
        1 // Default capacity for iCal bookings
    );

    if (!$lesson) {
        $_SESSION['error_message'] = 'Could not find or create lesson.';
        header('Location: slot_picker.php');
        exit;
    }

    $lessonId = $lesson['id'];

    if (!$lesson) {
        $_SESSION['error_message'] = 'Lesson not found.';
        header('Location: slot_picker.php');
        exit;
    }

    // Basic capacity check (more robust checks needed for race conditions)
    if ($lesson['capacity'] <= 0) {
        $_SESSION['error_message'] = 'This lesson is fully booked.';
        header('Location: slot_picker.php');
        exit;
    }

    // Check if user has already booked this lesson
    if ($bookingModel->hasUserBookedLesson($userId, $lessonId)) {
        $_SESSION['error_message'] = 'You have already booked this lesson.';
        header('Location: slot_picker.php');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Create booking
        $bookingId = $bookingModel->create($lessonId, $userId);

        // Decrease lesson capacity
        $lessonModel->decreaseCapacity($lessonId);

        $pdo->commit();

        // Generate iCal event
        $eventData = [
            'uid' => 'booking-' . $bookingId . '@safehavendutch.com',
            'summary' => 'Dutch Lesson: ' . $lesson['title'],
            'description' => $lesson['description'],
            'dtstart' => str_replace(['-', ':'], ['', ''], date('Ymd\THis', strtotime($lesson['start_time']))),
            'dtend' => str_replace(['-', ':'], ['', ''], date('Ymd\THis', strtotime($lesson['end_time']))),
            'location' => 'Online', // Assuming online lessons
        ];
        $icalContent = $icalGenerator->generateEvent($eventData);

        // Send confirmation email (assuming user email is available via session or user model)
        $user = $userModel->find($userId);
        if ($user) {
            $subject = 'Lesson Booking Confirmation';
            $body = "Dear " . htmlspecialchars($user['name']) . ",\n\n";
            $body .= "Your booking for the lesson \"" . htmlspecialchars($lesson['title']) . "\" has been confirmed.\n";
            $body .= "Date: " . htmlspecialchars(date('F j, Y', strtotime($lesson['start_time']))) . "\n";
            $body .= "Time: " . htmlspecialchars(date('g:i A', strtotime($lesson['start_time']))) . " - " . htmlspecialchars(date('g:i A', strtotime($lesson['end_time']))) . "\n\n";
            $body .= "You can add this to your calendar using the attached .ics file.\n\n";
            $body .= "Thank you,\nSafe Haven Dutch";

            $mailer->sendEmail($user['email'], $subject, $body, [], $icalContent);
        }

        // Trigger .ics file download
        header('Content-Type: text/calendar');
        header('Content-Disposition: attachment; filename=\"lesson_booking.ics\"');
        echo $icalContent;
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Booking error: " . $e->getMessage());
        $_SESSION['error_message'] = 'An error occurred during booking. Please try again.';
        header('Location: slot_picker.php');
        exit;
    }
} else {
    header('Location: slot_picker.php');
    exit;
}
