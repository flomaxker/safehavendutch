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

    public function create(array $data): bool
    {
        $sql = "INSERT INTO lessons (title, description, teacher_id, start_time, end_time, capacity) VALUES (:title, :description, :teacher_id, :start_time, :end_time, :capacity)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'teacher_id' => $data['teacher_id'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'capacity' => $data['capacity'],
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lessons WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
        return $lesson ?: null;
    }

    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $stmt = $this->pdo->query("SELECT * FROM lessons ORDER BY $orderBy $orderDirection");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE lessons SET title = :title, description = :description, teacher_id = :teacher_id, start_time = :start_time, end_time = :end_time, capacity = :capacity WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'teacher_id' => $data['teacher_id'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'capacity' => $data['capacity'],
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
