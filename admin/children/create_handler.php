<?php
declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

// Admin-only access guard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: /admin/children/create.php');
    exit;
}

// CSRF validation
$posted_token = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || !hash_equals((string) $_SESSION['csrf_token'], (string) $posted_token)) {
    $_SESSION['error_message'] = 'Invalid CSRF token.';
    header('Location: /admin/children/create.php');
    exit;
}

$pdo = $container->getPdo();

// Gather and sanitize input
$user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
$name = trim((string) ($_POST['name'] ?? ''));
$date_of_birth = trim((string) ($_POST['date_of_birth'] ?? ''));
$notes = trim((string) ($_POST['notes'] ?? ''));

// Preserve old input on error
$_SESSION['old_input'] = [
    'user_id' => $user_id,
    'name' => $name,
    'date_of_birth' => $date_of_birth,
    'notes' => $notes,
];

$errors = [];

// Validate user_id corresponds to a parent (treat 'member' as parent; include 'parent' for future compatibility)
if ($user_id <= 0) {
    $errors[] = 'Please select a parent.';
} else {
    $role_check = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role IN ('member','parent')");
    $role_check->execute(['id' => $user_id]);
    if (!$role_check->fetchColumn()) {
        $errors[] = 'Selected user is not a valid parent.';
    }
}

// Validate name
if ($name === '') {
    $errors[] = "Child's name is required.";
} elseif (mb_strlen($name) > 255) {
    $errors[] = "Child's name must be at most 255 characters.";
}

// Validate date_of_birth if provided
$dob_for_db = null;
if ($date_of_birth !== '') {
    $is_valid_format = (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth);
    if (!$is_valid_format) {
        $errors[] = 'Date of birth must be in YYYY-MM-DD format.';
    } else {
        try {
            $dob = new DateTime($date_of_birth);
            $today = new DateTime('today');
            if ($dob > $today) {
                $errors[] = 'Date of birth cannot be in the future.';
            } else {
                $dob_for_db = $dob->format('Y-m-d');
            }
        } catch (Throwable $e) {
            $errors[] = 'Invalid date of birth provided.';
        }
    }
}

if (!empty($errors)) {
    $_SESSION['error_message'] = 'Please correct the errors below.';
    $_SESSION['form_errors'] = $errors;
    header('Location: /admin/children/create.php');
    exit;
}

// Insert into database
try {
    $stmt = $pdo->prepare(
        'INSERT INTO children (user_id, name, date_of_birth, notes) VALUES (:user_id, :name, :date_of_birth, :notes)'
    );
    $stmt->execute([
        'user_id' => $user_id,
        'name' => $name,
        'date_of_birth' => $dob_for_db,
        'notes' => $notes !== '' ? $notes : null,
    ]);

    unset($_SESSION['old_input']);
    $_SESSION['success_message'] = 'Child created successfully.';
    header('Location: /admin/children/index.php');
    exit;
} catch (Throwable $e) {
    // Log and report generic error
    error_log('Create child failed: ' . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while creating the child.';
    header('Location: /admin/children/create.php');
    exit;
}

