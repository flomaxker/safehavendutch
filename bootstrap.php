<?php
use Dotenv\Dotenv;
use Stripe\StripeClient;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Container.php';
require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Models/Package.php';
require_once __DIR__ . '/app/Models/Purchase.php';
require_once __DIR__ . '/app/PaymentHandler.php';

session_start();

$dotenv = Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

$container = new Container();

$container->set('pdo', function () {
    $db = new Database();
    return $db->getConnection();
});

$container->set('packageModel', function ($c) {
    return new Package($c->get('pdo'));
});

$container->set('purchaseModel', function ($c) {
    return new Purchase($c->get('pdo'));
});

$container->set('stripe', function () {
    return new StripeClient(getenv('STRIPE_SECRET_KEY'));
});

$container->set('paymentHandler', function ($c) {
    return new PaymentHandler(
        $c->get('pdo'),
        $c->get('packageModel'),
        $c->get('purchaseModel'),
        $c->get('stripe')
    );
});
