<?php

namespace App\Models;

use PDO;

class Lesson
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(string $title, string $description, int $teacherId, string $startTime, string $endTime, int $capacity): int
    {
        $sql = "INSERT INTO lessons (title, description, teacher_id, start_time, end_time, capacity) VALUES (:title, :description, :teacher_id, :start_time, :end_time, :capacity)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'teacher_id' => $teacherId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'capacity' => $capacity,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findOrCreate(string $title, string $description, int $teacherId, string $startTime, string $endTime, int $capacity): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lessons WHERE title = :title AND teacher_id = :teacher_id AND start_time = :start_time AND end_time = :end_time");
        $stmt->execute([
            'title' => $title,
            'teacher_id' => $teacherId,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lesson) {
            return $lesson;
        } else {
            $newLessonId = $this->create($title, $description, $teacherId, $startTime, $endTime, $capacity);
            return $this->find($newLessonId);
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lessons WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        return $lesson ?: null;
    }

    public function decreaseCapacity(int $lessonId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE lessons SET capacity = capacity - 1 WHERE id = :lessonId AND capacity > 0');
        return $stmt->execute(['lessonId' => $lessonId]);
    }

    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $stmt = $this->pdo->query("SELECT * FROM lessons ORDER BY $orderBy $orderDirection");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $title, string $description, int $teacherId, string $startTime, string $endTime, int $capacity): bool
    {
        $sql = "UPDATE lessons SET title = :title, description = :description, teacher_id = :teacher_id, start_time = :start_time, end_time = :end_time, capacity = :capacity WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'teacher_id' => $teacherId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'capacity' => $capacity,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM lessons WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function deleteMany(array $ids): bool
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM lessons WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }
}
