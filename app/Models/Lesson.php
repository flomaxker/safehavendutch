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
        $stmt = $this->pdo->prepare(
            'INSERT INTO lessons (title, description, teacher_id, start_time, end_time, capacity)
             VALUES (:title, :description, :teacher_id, :start_time, :end_time, :capacity)'
        );
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

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM lessons WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM lessons ORDER BY start_time ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $title, string $description, int $teacherId, string $startTime, string $endTime, int $capacity): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE lessons SET title = :title, description = :description, teacher_id = :teacher_id, start_time = :start_time, end_time = :end_time, capacity = :capacity WHERE id = :id'
        );
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
        $stmt = $this->pdo->prepare('DELETE FROM lessons WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function decreaseCapacity(int $lessonId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE lessons SET capacity = capacity - 1 WHERE id = :lesson_id AND capacity > 0');
        return $stmt->execute(['lesson_id' => $lessonId]);
    }

    public function findOrCreate(string $title, string $description, int $teacherId, string $startTime, string $endTime, int $capacity): ?array
    {
        // Try to find an existing lesson first
        $stmt = $this->pdo->prepare(
            'SELECT * FROM lessons WHERE title = :title AND teacher_id = :teacher_id AND start_time = :start_time AND end_time = :end_time'
        );
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
            // If not found, create a new one
            $id = $this->create($title, $description, $teacherId, $startTime, $endTime, $capacity);
            return $this->getById($id);
        }
    }
}