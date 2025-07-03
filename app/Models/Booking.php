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

    public function create(int $lessonId, int $userId): int
    {
        $sql = "INSERT INTO bookings (lesson_id, user_id) VALUES (:lesson_id, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'lesson_id' => $lessonId,
            'user_id' => $userId,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function hasUserBookedLesson(int $userId, int $lessonId): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = :user_id AND lesson_id = :lesson_id");
        $stmt->execute([
            'user_id' => $userId,
            'lesson_id' => $lessonId
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: null;
    }

    public function findByLessonId(int $lessonId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE lesson_id = :lesson_id");
        $stmt->execute(['lesson_id' => $lessonId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM bookings WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $sql = "
            SELECT 
                b.id, 
                b.lesson_id, 
                b.user_id, 
                b.created_at,
                u.name as user_name,
                l.title as lesson_title
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN lessons l ON b.lesson_id = l.id
            ORDER BY $orderBy $orderDirection
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM bookings WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function deleteMany(array $ids): bool
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM bookings WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }
}
