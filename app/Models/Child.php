<?php

namespace App\Models;

use PDO;

class Child
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO children (user_id, name, date_of_birth, notes) VALUES (:user_id, :name, :date_of_birth, :notes)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM children WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $child = $stmt->fetch(PDO::FETCH_ASSOC);
        return $child ?: null;
    }

    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $stmt = $this->pdo->query("SELECT c.*, u.name as user_name FROM children c JOIN users u ON c.user_id = u.id ORDER BY $orderBy $orderDirection");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE children SET user_id = :user_id, name = :name, date_of_birth = :date_of_birth, notes = :notes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM children WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function deleteMany(array $ids): bool
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM children WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM children WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
