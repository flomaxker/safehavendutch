<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/Database.php';

class DatabaseTest extends TestCase
{
    public function testGetConnection()
    {
        $db = new Database();
        $pdo = $db->getConnection();
        $this->assertInstanceOf(PDO::class, $pdo);

        $stmt = $pdo->query('SELECT 1');
        $this->assertNotFalse($stmt);

        $result = $stmt->fetchColumn();
        $this->assertEquals('1', $result);
    }
}