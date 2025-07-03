<?php

use PHPUnit\Framework\TestCase;
use App\Models\Package;

/**
 * Tests for the Package model using an in-memory SQLite database.
 */
class PackageModelTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(
            'CREATE TABLE packages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                description TEXT,
                euro_value INTEGER,
                price_cents INTEGER,
                active INTEGER
            )'
        );
    }

    public function testGetAll(): void
    {
        $this->pdo->exec("INSERT INTO packages (name, description, euro_value, price_cents, active) VALUES ('A', 'desc', 1, 100, 1)");
        $this->pdo->exec("INSERT INTO packages (name, description, euro_value, price_cents, active) VALUES ('B', 'desc', 2, 200, 1)");

        $model = new Package($this->pdo);
        $all = $model->getAll();

        $this->assertCount(2, $all);
        $this->assertEquals('A', $all[0]['name']);
    }

    public function testGetById(): void
    {
        $this->pdo->exec("INSERT INTO packages (name, description, euro_value, price_cents, active) VALUES ('A', 'desc', 1, 100, 1)");
        $id = (int)$this->pdo->lastInsertId();

        $model = new Package($this->pdo);
        $package = $model->getById($id);

        $this->assertNotNull($package);
        $this->assertEquals('A', $package['name']);
    }

    public function testCreate(): void
    {
        $model = new Package($this->pdo);
        $id = $model->create('A', 'desc', 1, 100, true);

        $stmt = $this->pdo->query('SELECT * FROM packages WHERE id = ' . (int)$id);
        $data = $stmt->fetch();

        $this->assertEquals('A', $data['name']);
        $this->assertEquals(1, $data['euro_value']);
    }

    public function testUpdate(): void
    {
        $this->pdo->exec("INSERT INTO packages (name, description, euro_value, price_cents, active) VALUES ('A', 'desc', 1, 100, 1)");
        $id = (int)$this->pdo->lastInsertId();

        $model = new Package($this->pdo);
        $model->update($id, 'B', 'new', 2, 200, false);

        $stmt = $this->pdo->query('SELECT * FROM packages WHERE id = ' . $id);
        $pkg = $stmt->fetch();

        $this->assertEquals('B', $pkg['name']);
        $this->assertEquals(0, $pkg['active']);
    }

    public function testDelete(): void
    {
        $this->pdo->exec("INSERT INTO packages (name, description, euro_value, price_cents, active) VALUES ('A', 'desc', 1, 100, 1)");
        $id = (int)$this->pdo->lastInsertId();

        $model = new Package($this->pdo);
        $result = $model->delete($id);

        $this->assertTrue($result);
        $count = $this->pdo->query('SELECT COUNT(*) FROM packages')->fetchColumn();
        $this->assertEquals(0, $count);
    }
}