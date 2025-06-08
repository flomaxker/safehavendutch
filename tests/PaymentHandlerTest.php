<?php

// Minimal Stripe stubs so PaymentHandler can be loaded without the real library
namespace Stripe {
    class SessionsStub {
        public $lastParams;
        public function create($params) {
            $this->lastParams = $params;
            return (object)['id' => 'sess_123', 'url' => 'https://example.com'];
        }
    }
    class CheckoutStub {
        public $sessions;
        public function __construct() {
            $this->sessions = new SessionsStub();
        }
    }
    class StripeClient {
        public $checkout;
        public function __construct($key) {
            $this->checkout = new CheckoutStub();
        }
    }
    class Webhook {
        public static $lastArgs;
        public static function constructEvent($payload, $sigHeader, $secret) {
            self::$lastArgs = [$payload, $sigHeader, $secret];
            return json_decode($payload);
        }
    }
}

namespace {
use PHPUnit\Framework\TestCase;
use App\Payment\PaymentHandler;
use App\Models\Package;
use App\Models\Purchase;

class PaymentHandlerTest extends TestCase
{
    private function createDatabase(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, password TEXT, credit_balance INTEGER DEFAULT 0)');
        $pdo->exec('CREATE TABLE packages (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, description TEXT, credit_amount INTEGER, price_cents INTEGER, active INTEGER)');
        $pdo->exec('CREATE TABLE purchases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            package_id INTEGER,
            stripe_session_id TEXT,
            amount_cents INTEGER,
            status TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP
        )');
        return $pdo;
    }

    public function testCreateCheckoutSession(): void
    {
        $pdo = $this->createDatabase();
        $pdo->exec("INSERT INTO packages (name, description, credit_amount, price_cents, active) VALUES ('Pack', 'desc', 5, 1000, 1)");
        $packageId = (int)$pdo->lastInsertId();
        $pdo->exec("INSERT INTO users (name, email, password) VALUES ('User', 'user@example.com', 'secret')");
        $userId = (int)$pdo->lastInsertId();

        $stripeStub = new \Stripe\StripeClient('sk_test');
        $packageModel = new Package($pdo);
        $purchaseModel = new Purchase($pdo);
        $handler = new PaymentHandler($pdo, $packageModel, $purchaseModel, $stripeStub);

        $url = $handler->createCheckoutSession($userId, $packageId, 'http://success', 'http://cancel');

        $this->assertEquals('https://example.com', $url);
        $purchase = $pdo->query('SELECT * FROM purchases')->fetch();
        $params = $stripeStub->checkout->sessions->lastParams;
        $this->assertEquals($purchase['id'], $params['metadata']['purchase_id']);
        $this->assertEquals('pending', $purchase['status']);
        $this->assertEquals('sess_123', $purchase['stripe_session_id']);
    }

    public function testHandleWebhook(): void
    {
        $pdo = $this->createDatabase();
        $pdo->exec("INSERT INTO users (name, email, password, credit_balance) VALUES ('User', 'user@example.com', 'secret', 0)");
        $userId = (int)$pdo->lastInsertId();
        $pdo->exec("INSERT INTO packages (name, description, credit_amount, price_cents, active) VALUES ('Pack', 'desc', 5, 1000, 1)");
        $packageId = (int)$pdo->lastInsertId();
        $pdo->exec("INSERT INTO purchases (user_id, package_id, stripe_session_id, amount_cents, status) VALUES ($userId, $packageId, 'sess_123', 1000, 'pending')");
        $purchaseId = (int)$pdo->lastInsertId();

        $stripeStub = new \Stripe\StripeClient('sk_test');
        $packageModel = new Package($pdo);
        $purchaseModel = new Purchase($pdo);
        $handler = new PaymentHandler($pdo, $packageModel, $purchaseModel, $stripeStub);

        $event = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'metadata' => ['purchase_id' => $purchaseId]
                ]
            ]
        ];
        $payload = json_encode($event);
        $handler->handleWebhook($payload, 'sig', 'secret');

        $status = $pdo->query('SELECT status FROM purchases WHERE id = ' . $purchaseId)->fetchColumn();
        $balance = $pdo->query('SELECT credit_balance FROM users WHERE id = ' . $userId)->fetchColumn();
        $this->assertEquals('completed', $status);
        $this->assertEquals(5, $balance);
    }
}
}
