<?php

namespace App\Models;

use PDO;

class Booking
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $lessonId, int $userId, string $status = 'pending'): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO bookings (lesson_id, user_id, status)
             VALUES (:lesson_id, :user_id, :status)'
        );
        $stmt->execute([
            'lesson_id' => $lessonId,
            'user_id' => $userId,
            'status' => $status,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM bookings ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare('UPDATE bookings SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM bookings WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function hasUserBookedLesson(int $userId, int $lessonId): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM bookings WHERE user_id = :user_id AND lesson_id = :lesson_id');
        $stmt->execute([
            'user_id' => $userId,
            'lesson_id' => $lessonId,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public function getBookingsByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.*, l.title as lesson_title, l.start_time, l.end_time, u.name as teacher_name
             FROM bookings b
             JOIN lessons l ON b.lesson_id = l.id
             JOIN users u ON l.teacher_id = u.id
             WHERE b.user_id = :user_id
             ORDER BY l.start_time DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}