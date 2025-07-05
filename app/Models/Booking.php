<?php

namespace App\Models;

use App\Models\Lesson;
use App\Models\User;

class Booking
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM bookings ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function findAllWithDetails(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $allowedColumns = ['id', 'lesson_id', 'user_id', 'created_at']; // Add more as needed
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'id';
        }
        $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT 
                    b.id, 
                    b.created_at,
                    l.title AS lesson_title,
                    u.name AS user_name
                FROM bookings b
                JOIN lessons l ON b.lesson_id = l.id
                JOIN users u ON b.user_id = u.id
                ORDER BY $orderBy $orderDirection";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByUser($user_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function findByUserAndLesson($user_id, $lesson_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE user_id = :user_id AND lesson_id = :lesson_id');
        $stmt->execute(['user_id' => $user_id, 'lesson_id' => $lesson_id]);
        return $stmt->fetch();
    }

    public function findUpcomingWithDetailsByUserId(int $userId): array
    {
        // NOTE: NOW() is MySQL-specific. For SQLite (testing), CURRENT_TIMESTAMP is used.
        $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $now_function = ($driver === 'mysql') ? 'NOW()' : 'CURRENT_TIMESTAMP';

        $sql = "SELECT 
                    b.id,
                    l.title AS lesson_title,
                    l.start_time,
                    l.end_time,
                    t.name AS teacher_name
                FROM bookings b
                JOIN lessons l ON b.lesson_id = l.id
                JOIN users t ON l.teacher_id = t.id
                WHERE b.user_id = :user_id AND l.start_time >= $now_function
                ORDER BY l.start_time ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $this->pdo->beginTransaction();

        try {
            // 1. Get lesson details and lock the row for update if using MySQL
            $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $sql = 'SELECT * FROM lessons WHERE id = :id';
            if ($driver === 'mysql') {
                $sql .= ' FOR UPDATE';
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $data['lesson_id']]);
            $lesson = $stmt->fetch();

            if (!$lesson) {
                throw new \Exception("Lesson not found.");
            }

            // 2. Check lesson capacity
            $bookingCount = (new Lesson($this->pdo))->getBookingCount($data['lesson_id']);
            if ($bookingCount >= $lesson['capacity']) {
                throw new \Exception("This lesson is already full.");
            }

            // 3. Check user's balance
            $userModel = new User($this->pdo);
            $userBalance = $userModel->getEuroBalance($data['user_id']);
            if ($userBalance < $lesson['credit_cost']) {
                throw new \Exception("Insufficient credits to book this lesson.");
            }

            // 4. Create the booking
            $stmt = $this->pdo->prepare(
                'INSERT INTO bookings (user_id, lesson_id, child_id, status) 
                 VALUES (:user_id, :lesson_id, :child_id, :status)'
            );
            $stmt->execute([
                'user_id' => $data['user_id'],
                'lesson_id' => $data['lesson_id'],
                'child_id' => $data['child_id'],
                'status' => $data['status'] ?? 'confirmed',
            ]);
            $bookingId = $this->pdo->lastInsertId();

            // 5. Deduct credits from user's balance
            $userModel->updateEuroBalance($data['user_id'], -$lesson['credit_cost']);

            $this->pdo->commit();

            return $bookingId;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            // Re-throw the exception to be handled by the caller
            throw $e;
        }
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE bookings 
             SET status = :status
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM bookings WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deleteMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM bookings WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }
}
