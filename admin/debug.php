<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre>";
echo "<h1>Upload Diagnostics</h1>";

// --- 1. Check PHP Configuration ---
echo "<h2>PHP Configuration</h2>";
$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$open_basedir = ini_get('open_basedir');
$tmp_dir = sys_get_temp_dir();

echo "upload_max_filesize: " . $upload_max . "\n";
echo "post_max_size: " . $post_max . "\n";
echo "open_basedir: " . ($open_basedir ?: 'Not set') . "\n";
echo "Temporary Directory (sys_get_temp_dir): " . $tmp_dir . "\n";

// --- 2. Check Target Directory ---
echo "\n<h2>Target Directory</h2>";
$target_dir = __DIR__ . '/../uploads/';
echo "Attempting to use directory: " . realpath($target_dir) . "\n";

if (!is_dir($target_dir)) {
    die("<strong>Error:</strong> Target directory does not exist.");
}
if (!is_writable($target_dir)) {
    die("<strong>Error:</strong> Target directory is NOT WRITABLE by the web server.");
}
echo "Target directory exists and is writable.\n";


// --- 3. Handle File Upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "\n<h2>Upload Attempt</h2>";
    if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
        die("<strong>Error:</strong> No file data received. The form might be incorrect.");
    }

    $file = $_FILES['image'];

    echo "PHP received the following file data:\n";
    print_r($file);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("<strong>Upload Error Code:</strong> " . $file['error'] . " - Something went wrong with the upload.");
    }

    $destination = $target_dir . basename($file['name']);
    echo "\nAttempting to move '" . htmlspecialchars($file['tmp_name']) . "' to '" . htmlspecialchars($destination) . "'...\n";

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        echo "<strong><span style='color:green'>Success!</span></strong> File moved successfully.";
    } else {
        $error = error_get_last();
        echo "<strong><span style='color:red'>Failure!</span></strong> move_uploaded_file() failed.\n";
        echo "PHP Error Message: " . ($error['message'] ?? 'No error message available.');
    }
}

echo "</pre>";
?>

<hr>
<h2>Test Upload Form</h2>
<form action="debug.php" method="post" enctype="multipart/form-data">
    <p>Select a small image to upload:</p>
    <input type="file" name="image" id="image">
    <button type="submit">Upload Image</button>
</form>