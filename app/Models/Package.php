<?php
namespace App\Models;

/**
 * Model for lesson packages.
 */
class Package
{
    /** @var PDO */
    private $pdo;

    /**
     * Package constructor.
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all packages.
     * @return array
     */
    public function getAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM packages');
        return $stmt->fetchAll();
    }

    /**
     * Get a single package by ID.
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM packages WHERE id = :id');
        $stmt->execute(['id' => $id]);
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
    public function create($name, $description, $creditAmount, $priceCents, $active = true)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO packages (name, description, credit_amount, price_cents, active)
             VALUES (:name, :description, :credit_amount, :price_cents, :active)'
        );
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'credit_amount' => $creditAmount,
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
    public function update($id, $name, $description, $creditAmount, $priceCents, $active)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE packages SET name = :name, description = :description,
             credit_amount = :credit_amount, price_cents = :price_cents,
             active = :active WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'credit_amount' => $creditAmount,
            'price_cents' => $priceCents,
            'active' => $active ? 1 : 0,
        ]);
    }

    /**
     * Delete a package.
     * @param int $id
     * @return bool
     */
    public function delete($id)
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
    public function toggleActive($id, $active)
    {
        $stmt = $this->pdo->prepare('UPDATE packages SET active = :active WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'active' => $active ? 1 : 0,
        ]);
    }
}
