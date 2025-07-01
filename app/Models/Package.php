<?php

namespace App\Models;

use PDO;

/**
 * Model for lesson packages.
 */
class Package
{
    /** @var PDO */
    private PDO $pdo;

    /**
     * Package constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all packages.
     * @return array
     */
    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $stmt = $this->pdo->query("SELECT * FROM packages ORDER BY $orderBy $orderDirection");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single package by ID.
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM packages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get a single package by name.
     * @param string $name
     * @return array|null
     */
    public function findByName(string $name): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM packages WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a new package.
     * @param string $name
     * @param string $description
     * @param int $creditAmount
     * @param int $priceCents
     * @param bool $active
     * @return int Inserted package ID
     */
    public function create(string $name, string $description, int $euroValue, int $priceCents, bool $active = true): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO packages (name, description, euro_value, price_cents, active)
             VALUES (:name, :description, :euro_value, :price_cents, :active)'
        );
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'euro_value' => $euroValue,
            'price_cents' => $priceCents,
            'active' => $active ? 1 : 0,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update an existing package.
     * @param int $id
     * @param string $name
     * @param string $description
     * @param int $creditAmount
     * @param int $priceCents
     * @param bool $active
     * @return bool
     */
    public function update(int $id, string $name, string $description, int $euroValue, int $priceCents, bool $active): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE packages SET name = :name, description = :description,
             euro_value = :euro_value, price_cents = :price_cents,
             active = :active WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'euro_value' => $euroValue,
            'price_cents' => $priceCents,
            'active' => $active ? 1 : 0,
        ]);
    }

    /**
     * Delete a package.
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM packages WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Activate or deactivate a package.
     * @param int $id
     * @param bool $active
     * @return bool
     */
    public function toggleActive(int $id, bool $active): bool
    {
        $stmt = $this->pdo->prepare('UPDATE packages SET active = :active WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'active' => $active ? 1 : 0,
        ]);
    }

    public function deleteMany(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("DELETE FROM packages WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    public function toggleActiveMany(array $ids, bool $active): bool
    {
        if (empty($ids)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("UPDATE packages SET active = :active WHERE id IN ($placeholders)");
        $params = array_merge(['active' => $active ? 1 : 0], $ids);
        return $stmt->execute($params);
    }

    public function getTotalActivePackagesCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM packages WHERE active = 1");
        return (int)$stmt->fetchColumn();
    }
}

