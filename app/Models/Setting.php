<?php

namespace App\Models;

use PDO;

class Setting
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSetting(string $key): ?string
    {
        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['setting_value'] ?? null;
    }

    public function getAllSettings(): array
    {
        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM settings");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function updateSetting(string $key, string $value): bool
    {
        // Check if the setting already exists
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = :key");
        $stmt->execute(['key' => $key]);
        $exists = (bool)$stmt->fetchColumn();

        if ($exists) {
            // Update existing setting
            $stmt = $this->pdo->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
            return $stmt->execute(['key' => $key, 'value' => $value]);
        } else {
            // Insert new setting
            $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)");
            return $stmt->execute(['key' => $key, 'value' => $value]);
        }
    }
}
