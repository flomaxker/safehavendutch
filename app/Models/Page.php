<?php

namespace App\Models;

use App\Database\Database;
use PDO;

class Page
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        return $page ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO pages (slug, title, meta_description, og_title, og_description, og_url, og_image, content) VALUES (:slug, :title, :meta_description, :og_title, :og_description, :og_url, :og_image, :content)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'slug' => $data['slug'],
            'title' => $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_url' => $data['og_url'] ?? null,
            'og_image' => $data['og_image'] ?? null,
            'content' => $data['content'] ?? null,
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM pages ORDER BY title ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        return $page ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE pages SET title = :title, meta_description = :meta_description, og_title = :og_title, og_description = :og_description, og_url = :og_url, og_image = :og_image, content = :content, slug = :slug WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_url' => $data['og_url'] ?? null,
            'og_image' => $data['og_image'] ?? null,
            'content' => $data['content'] ?? null,
            'slug' => $data['slug'],
        ]);
    }
}
