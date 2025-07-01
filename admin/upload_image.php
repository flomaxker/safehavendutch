<?php
require_once __DIR__ . '/../bootstrap.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_FILES['image'])) {
    $file = $_FILES['image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'File upload error. Code: ' . $file['error']]);
        exit();
    }

    // More robust validation
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime_type, $allowed_mime_types)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid file type. Detected: ' . $mime_type]);
        exit();
    }

    $unique_filename = uniqid('img_', true) . '.png';
    $upload_path = PROJECT_ROOT . '/uploads/' . $unique_filename;

    // Use a more robust method to save the file
    $file_contents = file_get_contents($file['tmp_name']);
    if ($file_contents === false) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Could not read uploaded file from temporary location.']);
        exit();
    }

    if (file_put_contents($upload_path, $file_contents) !== false) {
        $public_url = '/uploads/' . $unique_filename;
        echo json_encode(['url' => $public_url]);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to write file to uploads directory.']);
    }

} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'No file uploaded.']);
}
