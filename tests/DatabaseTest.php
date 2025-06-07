<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/Database.php';

class DatabaseTest extends TestCase
{
    public function testGetConnection()
    {
        if (!getenv('DB_HOST')) {
            $this->markTestSkipped('Database credentials not configured');
        }

        try {
            $db = new Database();
            $pdo = $db->getConnection();
        } catch (PDOException $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
            return;
        }

        $this->assertInstanceOf(PDO::class, $pdo);

        $stmt = $pdo->query('SELECT 1');
        $this->assertNotFalse($stmt);

        $result = $stmt->fetchColumn();
        $this->assertEquals('1', $result);
    }
}