<?php
require_once __DIR__ . '/../../bootstrap.php';

// TODO: Add authentication and authorization check

$bookingModel = $container->getBookingModel();
$bookingModel->delete($_GET['id']);

header('Location: index.php');
exit;
