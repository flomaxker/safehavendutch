<?php

use PHPUnit\Framework\TestCase;
use App\Database\Database;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Booking;
use App\Models\Child;

class BookingIntegrationTest extends TestCase
{
    private $pdo;
    private $userModel;
    private $lessonModel;
    private $bookingModel;
    private $childModel;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create tables with SQLite-compatible syntax
        $this->pdo->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'member',
            euro_balance INTEGER NOT NULL DEFAULT 0,
            quick_actions_order TEXT,
            ical_url TEXT,
            last_login_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->pdo->exec("CREATE TABLE children (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            date_of_birth DATE NOT NULL,
            avatar TEXT,
            notes TEXT,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");

        $this->pdo->exec("CREATE TABLE lessons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            teacher_id INTEGER NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME NOT NULL,
            capacity INTEGER NOT NULL,
            credit_cost INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (teacher_id) REFERENCES users(id)
        )");

        $this->pdo->exec("CREATE TABLE bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            lesson_id INTEGER NOT NULL,
            child_id INTEGER NOT NULL,
            status TEXT NOT NULL DEFAULT 'confirmed',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (lesson_id) REFERENCES lessons(id),
            FOREIGN KEY (child_id) REFERENCES children(id)
        )");

        $this->userModel = new User($this->pdo);
        $this->lessonModel = new Lesson($this->pdo);
        $this->bookingModel = new Booking($this->pdo);
        $this->childModel = new Child($this->pdo);

        // Seed a test user, teacher, and child
        $this->userModel->create('Test Parent', 'parent@example.com', password_hash('password', PASSWORD_DEFAULT), 'parent', 500);
        $this->userModel->create('Test Teacher', 'teacher@example.com', password_hash('password', PASSWORD_DEFAULT), 'teacher');
        $parent = $this->userModel->findByEmail('parent@example.com');
        $this->childModel->create(['user_id' => $parent['id'], 'name' => 'Test Child', 'date_of_birth' => '2018-01-01']);
    }

    public function testUserCanBookLessonAndCreditsAreDeducted(): void
    {
        $parent = $this->userModel->findByEmail('parent@example.com');
        $teacher = $this->userModel->findByEmail('teacher@example.com');
        $child = $this->childModel->findByUserId($parent['id'])[0];

        $this->lessonModel->create([
            'title' => 'Test Lesson', 
            'description' => 'A lesson for testing.', 
            'teacher_id' => $teacher['id'], 
            'start_time' => '2025-08-01 10:00:00', 
            'end_time' => '2025-08-01 11:00:00', 
            'capacity' => 1, 
            'credit_cost' => 100
        ]);
        $lessonId = $this->pdo->lastInsertId();

        $bookingId = $this->bookingModel->create([
            'user_id' => $parent['id'],
            'lesson_id' => $lessonId,
            'child_id' => $child['id']
        ]);

        $this->assertNotNull($bookingId);
        $booking = $this->bookingModel->find($bookingId);
        $this->assertEquals('confirmed', $booking['status']);

        $updatedUser = $this->userModel->find($parent['id']);
        $this->assertEquals(400, $updatedUser['euro_balance']);
    }

    public function testUserCannotBookFullLesson(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This lesson is already full.');

        $parent = $this->userModel->findByEmail('parent@example.com');
        $teacher = $this->userModel->findByEmail('teacher@example.com');
        $child = $this->childModel->findByUserId($parent['id'])[0];

        $this->lessonModel->create([
            'title' => 'Full Lesson', 
            'description' => 'A lesson with no spots.', 
            'teacher_id' => $teacher['id'], 
            'start_time' => '2025-08-01 10:00:00', 
            'end_time' => '2025-08-01 11:00:00', 
            'capacity' => 0, 
            'credit_cost' => 100
        ]);
        $lessonId = $this->pdo->lastInsertId();

        try {
            $this->bookingModel->create([
                'user_id' => $parent['id'],
                'lesson_id' => $lessonId,
                'child_id' => $child['id']
            ]);
        } catch (\Exception $e) {
            // Verify user balance is unchanged
            $user = $this->userModel->find($parent['id']);
            $this->assertEquals(500, $user['euro_balance']);
            throw $e; // Re-throw exception for PHPUnit to catch
        }
    }

    public function testUserCannotBookWithInsufficientCredits(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient credits to book this lesson.');

        $parent = $this->userModel->findByEmail('parent@example.com');
        $teacher = $this->userModel->findByEmail('teacher@example.com');
        $child = $this->childModel->findByUserId($parent['id'])[0];

        $this->lessonModel->create([
            'title' => 'Expensive Lesson', 
            'description' => 'A very expensive lesson.', 
            'teacher_id' => $teacher['id'], 
            'start_time' => '2025-08-01 10:00:00', 
            'end_time' => '2025-08-01 11:00:00', 
            'capacity' => 1, 
            'credit_cost' => 600
        ]);
        $lessonId = $this->pdo->lastInsertId();

        try {
            $this->bookingModel->create([
                'user_id' => $parent['id'],
                'lesson_id' => $lessonId,
                'child_id' => $child['id']
            ]);
        } catch (\Exception $e) {
            // Verify user balance is unchanged
            $user = $this->userModel->find($parent['id']);
            $this->assertEquals(500, $user['euro_balance']);
            throw $e; // Re-throw exception for PHPUnit to catch
        }
    }
}