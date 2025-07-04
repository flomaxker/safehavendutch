<?php

namespace App\Models;

class Booking
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM bookings ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByUser($user_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO bookings (user_id, lesson_id, status) 
             VALUES (:user_id, :lesson_id, :status)'
        );
        return $stmt->execute([
            'user_id' => $data['user_id'],
            'lesson_id' => $data['lesson_id'],
            'status' => $data['status'] ?? 'confirmed',
        ]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE bookings 
             SET status = :status
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM bookings WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
