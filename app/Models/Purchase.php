<?php

namespace App\Models;

use PDO;

/**
 * Model for purchases records.
 */
class Purchase
{
    /** @var PDO */
    private PDO $pdo;

    /**
     * Purchase constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new purchase record.
     * @param int $userId
     * @param int $packageId
     * @param string $stripeSessionId
     * @param int $amountCents
     * @param string $status
     * @return int Inserted purchase ID
     */
    public function create(int $userId, int $packageId, string $stripeSessionId, int $amountCents, string $status): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO purchases (user_id, package_id, stripe_session_id, amount_cents, status)
             VALUES (:user_id, :package_id, :stripe_session_id, :amount_cents, :status)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'package_id' => $packageId,
            'stripe_session_id' => $stripeSessionId,
            'amount_cents' => $amountCents,
            'status' => $status,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update stripe_session_id for a purchase.
     * @param int $id
     * @param string $stripeSessionId
     * @return bool
     */
    public function updateSessionId(int $id, string $stripeSessionId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE purchases SET stripe_session_id = :stripe_session_id WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'stripe_session_id' => $stripeSessionId,
        ]);
    }

    /**
     * Update status for a purchase by ID.
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatusById(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE purchases SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    /**
     * Get a purchase by ID.
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM purchases WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get all purchases for a given user ID.
     * @param int $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT p.*, pk.name as package_name, pk.euro_value FROM purchases p JOIN packages pk ON p.package_id = pk.id WHERE p.user_id = :user_id ORDER BY p.created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
