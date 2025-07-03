<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Models\User;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $order = $input['order'] ?? [];
    $userId = $input['userId'] ?? null;

    if (empty($order) || $userId === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data provided.']);
        exit;
    }

    $db = new Database();
    $pdo = $db->getConnection();
    $userModel = new User($pdo);

    try {
        // Fetch the current user to get existing data for update method
        $user = $userModel->find($userId); // Use find by ID directly

        if ($user && $user['id'] == $userId) {
            // Update only the quick_actions_order column
            if ($userModel->updateQuickActionsOrder($userId, json_encode($order))) {
                echo json_encode(['status' => 'success', 'message' => 'Quick actions order saved.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save quick actions order.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized user or user not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}