<?php

use PHPUnit\Framework\TestCase;
use App\Database\Database;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Booking;
use App\Calendar\iCalParser;

class BookingIntegrationTest extends TestCase
{
    private $pdo;
    private $userModel;
    private $lessonModel;
    private $bookingModel;
    private $iCalParser;

    protected function setUp(): void
    {
        // Use an in-memory SQLite database for testing
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create necessary tables (simplified for test)
        $this->pdo->exec('CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT UNIQUE,
            password TEXT,
            role TEXT,
            euro_balance INTEGER DEFAULT 0,
            ical_url TEXT,
            quick_actions_order TEXT
        )');
        $this->pdo->exec('CREATE TABLE lessons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            description TEXT,
            teacher_id INTEGER,
            start_time DATETIME,
            end_time DATETIME,
            capacity INTEGER DEFAULT 1,
            credit_cost INTEGER DEFAULT 0
        )');
        $this->pdo->exec('CREATE TABLE bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            lesson_id INTEGER,
            status TEXT
        )');

        $this->userModel = new User($this->pdo);
        $this->lessonModel = new Lesson($this->pdo);
        $this->bookingModel = new Booking($this->pdo);
        $this->iCalParser = new iCalParser();

        // Seed a test user
        $this->userModel->create('Test User', 'test@example.com', password_hash('password', PASSWORD_DEFAULT), 'member', 500);
        $this->userModel->create('Test Teacher', 'teacher@example.com', password_hash('password', PASSWORD_DEFAULT), 'teacher', 0, '[]', 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:test-event-1@example.com
DTSTAMP:20250710T090000Z
DTSTART:20250710T100000Z
DTEND:20250710T110000Z
SUMMARY:Dutch Lesson
END:VEVENT
END:VCALENDAR');
    }

    public function testUserCanBookLessonAndCreditsAreDeducted(): void
    {
        // 1. Find the test user and teacher
        $user = $this->userModel->findByEmail('test@example.com');
        $teacher = $this->userModel->findByEmail('teacher@example.com');

        // 2. Create a lesson
        $lessonId = $this->lessonModel->create('Test Lesson', 'A lesson for testing.', $teacher['id'], '2025-07-10 10:00:00', '2025-07-10 11:00:00', 1, 100);

        // 3. Book the lesson
        $this->bookingModel->create($user['id'], $lessonId, 'confirmed');

        // 4. Assert booking exists
        $booking = $this->bookingModel->findByUserAndLesson($user['id'], $lessonId);
        $this->assertNotFalse($booking);
        $this->assertEquals('confirmed', $booking['status']);

        // 5. Assert credits were deducted
        $updatedUser = $this->userModel->find($user['id']);
        $this->assertEquals(400, $updatedUser['euro_balance']);
    }

    public function testUserCannotBookFullLesson(): void
    {
        // 1. Find the test user and teacher
        $user = $this->userModel->findByEmail('test@example.com');
        $teacher = $this->userModel->findByEmail('teacher@example.com');

        // 2. Create a lesson with 0 capacity
        $lessonId = $this->lessonModel->create('Full Lesson', 'A lesson with no spots.', $teacher['id'], '2025-07-11 10:00:00', '2025-07-11 11:00:00', 0, 100);

        // 3. Expect an exception when booking
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This lesson is full.');

        // 4. Attempt to book the lesson
        $this->bookingModel->create($user['id'], $lessonId, 'confirmed');
    }

    public function testUserCannotBookWithInsufficientCredits(): void
    {
        // 1. Find the test user and teacher
        $user = $this->userModel->findByEmail('test@example.com');
        $teacher = $this->userModel->findByEmail('teacher@example.com');

        // 2. Create a lesson that costs more than the user's balance
        $lessonId = $this->lessonModel->create('Expensive Lesson', 'A very expensive lesson.', $teacher['id'], '2025-07-12 10:00:00', '2025-07-12 11:00:00', 1, 600);

        // 3. Expect an exception when booking
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient credits.');

        // 4. Attempt to book the lesson
        $this->bookingModel->create($user['id'], $lessonId, 'confirmed');
    }
}