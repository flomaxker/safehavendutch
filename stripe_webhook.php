<?php
require_once __DIR__ . '/bootstrap.php';

$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$endpointSecret = getenv('STRIPE_WEBHOOK_SECRET');

$handler = $container->get('paymentHandler');

try {
    $handler->handleWebhook($payload, $sigHeader, $endpointSecret);
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    http_response_code(400);
    echo 'Error: ' . $e->getMessage();
}
