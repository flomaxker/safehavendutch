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
        $sql = "INSERT INTO pages (slug, title, meta_description, og_title, og_description, og_url, og_image, hero_title, hero_subtitle, main_content, show_contact_form, show_packages, about_hero_title, about_hero_subtitle, about_mission_heading, about_mission_text, about_mission_image, about_founder_heading, about_founder_image, about_founder_name, about_founder_title, about_founder_quote, features_heading, feature1_icon, feature1_title, feature1_description, feature2_icon, feature2_title, feature2_description, feature3_icon, feature3_title, feature3_description, page_type) VALUES (:slug, :title, :meta_description, :og_title, :og_description, :og_url, :og_image, :hero_title, :hero_subtitle, :main_content, :show_contact_form, :show_packages, :about_hero_title, :about_hero_subtitle, :about_mission_heading, :about_mission_text, :about_mission_image, :about_founder_heading, :about_founder_image, :about_founder_name, :about_founder_title, :about_founder_quote, :features_heading, :feature1_icon, :feature1_title, :feature1_description, :feature2_icon, :feature2_title, :feature2_description, :feature3_icon, :feature3_title, :feature3_description, :page_type)";
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
            'about_hero_title' => $data['about_hero_title'] ?? null,
            'about_hero_subtitle' => $data['about_hero_subtitle'] ?? null,
            'about_mission_heading' => $data['about_mission_heading'] ?? null,
            'about_mission_text' => $data['about_mission_text'] ?? null,
            'about_mission_image' => $data['about_mission_image'] ?? null,
            'about_founder_heading' => $data['about_founder_heading'] ?? null,
            'about_founder_image' => $data['about_founder_image'] ?? null,
            'about_founder_name' => $data['about_founder_name'] ?? null,
            'about_founder_title' => $data['about_founder_title'] ?? null,
            'about_founder_quote' => $data['about_founder_quote'] ?? null,
            'features_heading' => $data['features_heading'] ?? null,
            'feature1_icon' => $data['feature1_icon'] ?? null,
            'feature1_title' => $data['feature1_title'] ?? null,
            'feature1_description' => $data['feature1_description'] ?? null,
            'feature2_icon' => $data['feature2_icon'] ?? null,
            'feature2_title' => $data['feature2_title'] ?? null,
            'feature2_description' => $data['feature2_description'] ?? null,
            'feature3_icon' => $data['feature3_icon'] ?? null,
            'feature3_title' => $data['feature3_title'] ?? null,
            'feature3_description' => $data['feature3_description'] ?? null,
            'page_type' => $data['page_type'] ?? 'standard',
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM pages ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, slug, title, meta_description, og_title, og_description, og_url, og_image, hero_title, hero_subtitle, main_content, show_contact_form, show_packages, about_hero_title, about_hero_subtitle, about_mission_heading, about_mission_text, about_mission_image, about_founder_heading, about_founder_image, about_founder_name, about_founder_title, about_founder_quote, page_type, features_heading, feature1_icon, feature1_title, feature1_description, feature2_icon, feature2_title, feature2_description, feature3_icon, feature3_title, feature3_description FROM pages WHERE id = :id");
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
