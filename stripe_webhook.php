<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Models/Purchase.php';
require_once __DIR__ . '/app/Models/Package.php';
require_once __DIR__ . '/app/PaymentHandler.php';

$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$endpointSecret = getenv('STRIPE_WEBHOOK_SECRET');

$db = new Database();
$pdo = $db->getConnection();
$handler = new PaymentHandler($pdo, getenv('STRIPE_SECRET_KEY'));

try {
    $handler->handleWebhook($payload, $sigHeader, $endpointSecret);
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    http_response_code(400);
    echo 'Error: ' . $e->getMessage();
}