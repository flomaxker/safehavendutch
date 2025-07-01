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

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT *, quick_actions_order FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT *, quick_actions_order FROM users WHERE email = :email');
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

    public function getEuroBalance(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT euro_balance FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetchColumn();
        return (int)$result;
    }

    public function create(string $name, string $email, string $password, string $role = 'student', int $euroBalance = 0, string $quickActionsOrder = '[]'): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password, role, euro_balance, quick_actions_order) VALUES (:name, :email, :password, :role, :euro_balance, :quick_actions_order)'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'euro_balance' => $euroBalance,
            'quick_actions_order' => $quickActionsOrder,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $allowedColumns = ['id', 'name', 'email', 'euro_balance', 'role', 'created_at'];
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'id'; // Default to id if invalid column is provided
        }

        $orderDirection = strtoupper($orderDirection);
        if (!in_array($orderDirection, ['ASC', 'DESC'])) {
            $orderDirection = 'ASC'; // Default to ASC if invalid direction is provided
        }

        $stmt = $this->pdo->query("SELECT id, name, email, euro_balance, role, created_at FROM users ORDER BY $orderBy $orderDirection");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUserIds(): array
    {
        $stmt = $this->pdo->query("SELECT id FROM users");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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

    public function updateEuroBalance(int $userId, int $amount): bool
    {
        $stmt = $this->pdo->prepare('UPDATE users SET euro_balance = euro_balance + :amount WHERE id = :userId');
        return $stmt->execute([
            'amount' => $amount,
            'userId' => $userId
        ]);
    }

    public function update(int $id, string $name, string $email, int $euroBalance, string $role, string $quickActionsOrder): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET name = :name, email = :email, euro_balance = :euro_balance, role = :role, quick_actions_order = :quick_actions_order WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'euro_balance' => $euroBalance,
            'role' => $role,
            'quick_actions_order' => $quickActionsOrder,
        ]);
    }

    public function updateQuickActionsOrder(int $userId, string $orderJson): bool
    {
        $stmt = $this->pdo->prepare('UPDATE users SET quick_actions_order = :orderJson WHERE id = :userId');
        return $stmt->execute([
            'orderJson' => $orderJson,
            'userId' => $userId
        ]);
    }

    public function anonymize(int $userId): bool
    {
        $this->pdo->beginTransaction();

        try {
            // Anonymize user's children first
            $childModel = new Child($this->pdo);
            $children = $childModel->findByUserId($userId);
            foreach ($children as $child) {
                $childModel->update($child['id'], [
                    'name' => 'Anonymized Child',
                    'date_of_birth' => '1970-01-01',
                    'avatar' => null,
                    'notes' => 'User data anonymized.'
                ]);
            }

            // Anonymize the user
            $anonymizedEmail = 'anonymized_' . uniqid() . '@example.com';
            $stmt = $this->pdo->prepare(
                "UPDATE users SET 
                    name = 'Anonymized User', 
                    email = :email, 
                    password = '', 
                    quick_actions_order = '[]'
                WHERE id = :id"
            );
            $stmt->execute([
                'email' => $anonymizedEmail,
                'id' => $userId
            ]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            // Optionally log the error: error_log($e->getMessage());
            return false;
        }
    }

    public function recordLoginAttempt(int $userId, bool $successful): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO login_attempts (user_id, ip_address, successful) VALUES (:user_id, :ip_address, :successful)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'successful' => (int)$successful,
        ]);
    }

    public function getFailedLoginAttempts(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM login_attempts WHERE user_id = :user_id AND successful = 0 AND attempted_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)'
        );
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public function getLastFailedLoginAttemptTime(int $userId): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT attempted_at FROM login_attempts WHERE user_id = :user_id AND successful = 0 ORDER BY attempted_at DESC LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetchColumn();
        return $result ?: null;
    }

    public function updateLastLogin(int $userId): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }

    public function findInactiveUsers(int $months): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, last_login_at FROM users WHERE last_login_at < DATE_SUB(NOW(), INTERVAL :months MONTH) OR (last_login_at IS NULL AND created_at < DATE_SUB(NOW(), INTERVAL :months MONTH))'
        );
        $stmt->execute(['months' => $months]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
