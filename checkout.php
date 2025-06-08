<?php

require_once __DIR__ . '/bootstrap.php';

use App\Database\Database;
use App\Models\Package;
use App\Models\Purchase;
use App\Payment\PaymentHandler;

session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$packageId = (int)($_GET['package_id'] ?? 0);
$db = new Database();
$pdo = $db->getConnection();
$packageModel = new Package($pdo);
$package = $packageModel->getById($packageId);

if (!$package || !$package['active']) {
    http_response_code(404);
    echo 'Package not found';
    exit;
}

$stripeSecret = getenv('STRIPE_SECRET_KEY');
$successUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/checkout_success.php';
$cancelUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/checkout_cancel.php';

$stripe = new \Stripe\StripeClient($stripeSecret);
$purchaseModel = new Purchase($pdo);
$handler = new PaymentHandler($pdo, $packageModel, $purchaseModel, $stripe);

try {
    $sessionUrl = $handler->createCheckoutSession($_SESSION['user_id'], $packageId, $successUrl, $cancelUrl);
    header('Location: ' . $sessionUrl);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . htmlspecialchars($e->getMessage());
    exit;
}