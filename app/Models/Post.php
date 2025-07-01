<?php

namespace App\Models;

use PDO;

class Post
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): int|false
    {
        $sql = "INSERT INTO posts (user_id, title, slug, content, status, published_at) VALUES (:user_id, :title, :slug, :content, :status, :published_at)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'status' => $data['status'],
            'published_at' => $data['published_at'],
        ]);

        if ($result) {
            return (int)$this->pdo->lastInsertId();
        }
        return false;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post ?: null;
    }

    public function getAll(string $orderBy = 'id', string $orderDirection = 'ASC'): array
    {
        $sql = "
            SELECT p.*, u.name as author_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY $orderBy $orderDirection
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT p.*, u.name as author_name FROM posts p JOIN users u ON p.user_id = u.id WHERE p.slug = :slug AND p.status = 'published'");
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post ?: null;
    }

    public function getPublishedPosts(string $orderBy = 'published_at', string $orderDirection = 'DESC', ?int $limit = null): array
    {
        $sql = "
            SELECT p.*, u.name as author_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.status = 'published' AND p.published_at <= NOW()
            ORDER BY $orderBy $orderDirection
        ";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE posts SET title = :title, slug = :slug, content = :content, status = :status, published_at = :published_at WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'status' => $data['status'],
            'published_at' => $data['published_at'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getPostCategories(int $postId): array
    {
        $stmt = $this->pdo->prepare("SELECT category_id FROM post_categories WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function syncCategories(int $postId, array $categoryIds): void
    {
        // First, remove existing categories for the post
        $stmt = $this->pdo->prepare("DELETE FROM post_categories WHERE post_id = :post_id");
        $stmt->execute(['post_id' => $postId]);

        // Then, add the new categories
        if (!empty($categoryIds)) {
            $sql = "INSERT INTO post_categories (post_id, category_id) VALUES ";
            $placeholders = [];
            $values = [];
            foreach ($categoryIds as $categoryId) {
                $placeholders[] = "(?, ?)";
                $values[] = $postId;
                $values[] = $categoryId;
            }
            $sql .= implode(', ', $placeholders);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
        }
    }
}
