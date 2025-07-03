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
        $sql = "INSERT INTO pages (slug, title, meta_description, og_title, og_description, og_url, og_image, hero_title, hero_subtitle, main_content, show_contact_form, show_packages) VALUES (:slug, :title, :meta_description, :og_title, :og_description, :og_url, :og_image, :hero_title, :hero_subtitle, :main_content, :show_contact_form, :show_packages)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'slug' => $data['slug'],
            'title' => $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_url' => $data['og_url'] ?? null,
            'og_image' => $data['og_image'] ?? null,
            'hero_title' => $data['hero_title'] ?? null,
            'hero_subtitle' => $data['hero_subtitle'] ?? null,
            'main_content' => $data['main_content'] ?? null,
            'show_contact_form' => $data['show_contact_form'] ?? 0,
            'show_packages' => $data['show_packages'] ?? 0,
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
        // Build the SQL query dynamically based on the provided data
        $set_clauses = [];
        foreach ($data as $key => $value) {
            $set_clauses[] = "$key = :$key";
        }
        $sql = "UPDATE pages SET " . implode(', ', $set_clauses) . " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
}
