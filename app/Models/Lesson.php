<?php

namespace App\Models;

class Lesson
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $stmt = $this->pdo->query(
            'SELECT l.*, u.name as teacher_name 
             FROM lessons l
             JOIN users u ON l.teacher_id = u.id
             ORDER BY l.start_time ASC'
        );
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM lessons WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO lessons (title, description, teacher_id, start_time, end_time, capacity, credit_cost) 
             VALUES (:title, :description, :teacher_id, :start_time, :end_time, :capacity, :credit_cost)'
        );
        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'teacher_id' => $data['teacher_id'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'capacity' => $data['capacity'],
            'credit_cost' => $data['credit_cost'],
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE lessons 
             SET title = :title, description = :description, teacher_id = :teacher_id, 
                 start_time = :start_time, end_time = :end_time, capacity = :capacity, credit_cost = :credit_cost
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description'],
            'teacher_id' => $data['teacher_id'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'capacity' => $data['capacity'],
            'credit_cost' => $data['credit_cost'],
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM lessons WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
