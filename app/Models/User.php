<?php

namespace App\Models;

use PDO;

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE name = :name');
        $stmt->execute(['name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function getCredits(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT credit_balance FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetchColumn();
        return (int)$result;
    }

    public function create(string $name, string $email, string $password, string $role = 'student'): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $allowedColumns = ['id', 'name', 'email', 'credit_balance', 'role', 'created_at'];
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'id'; // Default to id if invalid column is provided
        }

        $orderDirection = strtoupper($orderDirection);
        if (!in_array($orderDirection, ['ASC', 'DESC'])) {
            $orderDirection = 'ASC'; // Default to ASC if invalid direction is provided
        }

        $stmt = $this->pdo->query("SELECT id, name, email, credit_balance, role, created_at FROM users ORDER BY $orderBy $orderDirection");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deleteMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    public function getTotalUsersCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        return (int)$stmt->fetchColumn();
    }

    public function updateCreditBalance(int $userId, int $amount): bool
    {
        $stmt = $this->pdo->prepare('UPDATE users SET credit_balance = credit_balance + :amount WHERE id = :userId');
        return $stmt->execute([
            'amount' => $amount,
            'userId' => $userId
        ]);
    }

    public function update(int $id, string $name, string $email, int $creditBalance, string $role): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET name = :name, email = :email, credit_balance = :credit_balance, role = :role WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'credit_balance' => $creditBalance,
            'role' => $role,
        ]);
    }
}
