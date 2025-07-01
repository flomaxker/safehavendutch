<?php
require_once __DIR__ . '/../../bootstrap.php';

// TODO: Add authentication and authorization check

$childModel = $container->getChildModel();
$childModel->delete($_GET['id']);

header('Location: index.php');
exit;
