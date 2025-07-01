<?php
require_once __DIR__ . '/../../bootstrap.php';

// TODO: Add authentication and authorization check

$lessonModel = $container->getLessonModel();
$lessonModel->delete($_GET['id']);

header('Location: index.php');
exit;
