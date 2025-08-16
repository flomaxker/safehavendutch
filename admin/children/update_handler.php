<?php
declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

// Admin-only access guard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: /admin/children/index.php');
    exit;
}

// CSRF validation
$posted_token = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || !hash_equals((string) $_SESSION['csrf_token'], (string) $posted_token)) {
    $_SESSION['error_message'] = 'Invalid CSRF token.';
    $redir_id = isset($_POST['child_id']) ? (int) $_POST['child_id'] : 0;
    header('Location: /admin/children/edit.php?id=' . $redir_id);
    exit;
}

$pdo = $container->getPdo();

// Gather input
$child_id = isset($_POST['child_id']) ? (int) $_POST['child_id'] : 0;
$name = trim((string) ($_POST['name'] ?? ''));
$date_of_birth = trim((string) ($_POST['date_of_birth'] ?? ''));
$notes = trim((string) ($_POST['notes'] ?? ''));

// Preserve old input on error
$_SESSION['old_input'] = [
    'name' => $name,
    'date_of_birth' => $date_of_birth,
    'notes' => $notes,
];

$errors = [];

if ($child_id <= 0) {
    $errors[] = 'Invalid child ID.';
}

// Validate child exists (and fetch existing row)
$existing_child = null;
if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT * FROM children WHERE id = :id');
    $stmt->execute([':id' => $child_id]);
    $existing_child = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$existing_child) {
        $errors[] = 'Child not found.';
    }
}

// Validate name
if ($name === '') {
    $errors[] = "Child's name is required.";
} elseif (mb_strlen($name) > 255) {
    $errors[] = "Child's name must be at most 255 characters.";
}

// Validate date_of_birth (required and not in future)
$dob_for_db = null;
if ($date_of_birth === '') {
    $errors[] = 'Date of birth is required.';
} else {
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
    header('Location: /admin/children/edit.php?id=' . (int) $child_id);
    exit;
}

// Optionally append audit log if column exists
$updated_audit_log = null;
try {
    $col_stmt = $pdo->prepare("SHOW COLUMNS FROM children LIKE 'audit_log'");
    $col_stmt->execute();
    $has_audit_log = (bool) $col_stmt->fetch();
    if ($has_audit_log) {
        $existing_log = (string) ($existing_child['audit_log'] ?? '');
        $admin_name = $_SESSION['user_name'] ?? 'Admin';
        $admin_id = (string) ($_SESSION['user_id'] ?? '');
        $timestamp = date('Y-m-d H:i:s');
        $new_entry = sprintf("Updated on %s by %s (ID: %s).\n", $timestamp, $admin_name, $admin_id);
        $updated_audit_log = $existing_log . $new_entry;
    }
} catch (Throwable $e) {
    // Ignore audit log issues silently for now
    $updated_audit_log = null;
}

// Perform update
try {
    if ($updated_audit_log !== null) {
        $stmt = $pdo->prepare('UPDATE children SET name = :name, date_of_birth = :dob, notes = :notes, audit_log = :audit WHERE id = :id');
        $stmt->execute([
            ':name' => $name,
            ':dob' => $dob_for_db,
            ':notes' => $notes !== '' ? $notes : null,
            ':audit' => $updated_audit_log,
            ':id' => $child_id,
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE children SET name = :name, date_of_birth = :dob, notes = :notes WHERE id = :id');
        $stmt->execute([
            ':name' => $name,
            ':dob' => $dob_for_db,
            ':notes' => $notes !== '' ? $notes : null,
            ':id' => $child_id,
        ]);
    }

    unset($_SESSION['old_input']);
    $_SESSION['success_message'] = 'Child updated successfully.';
    header('Location: /admin/children/index.php');
    exit;
} catch (Throwable $e) {
    error_log('Update child failed: ' . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while updating the child.';
    header('Location: /admin/children/edit.php?id=' . (int) $child_id);
    exit;
}

