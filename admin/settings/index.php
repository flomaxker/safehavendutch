<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Setting;

$settingModel = new Setting($container->getPdo());

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle TinyMCE API Key
    if (isset($_POST['tinymce_api_key'])) {
        $apiKey = $_POST['tinymce_api_key'] ?? '';
        if ($settingModel->updateSetting('tinymce_api_key', $apiKey)) {
            $message = 'Settings updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to update API key.';
            $messageType = 'error';
        }
    }

    // Handle Logo Upload
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
        $allowed_types = ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'];
        $file_type = $_FILES['site_logo']['type'];

        if (in_array($file_type, $allowed_types)) {
            $upload_dir = __DIR__ . '/../../assets/images/';
            $file_name = 'site-logo.' . pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_path)) {
                $logo_path = '/assets/images/' . $file_name;
                if ($settingModel->updateSetting('site_logo', $logo_path)) {
                    $message = 'Logo uploaded and settings saved!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to save logo path to database.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Failed to move uploaded file.';
                $messageType = 'error';
            }
        } else {
            $message = 'Invalid file type. Please upload a PNG, JPG, GIF, or SVG.';
            $messageType = 'error';
        }
    }

    // Handle Hero Image Upload
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
        $allowed_types = ['image/png', 'image/jpeg', 'image/gif'];
        $file_type = $_FILES['hero_image']['type'];

        if (in_array($file_type, $allowed_types)) {
            $upload_dir = __DIR__ . '/../../assets/images/';
            $file_name = 'hero-image.' . pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $upload_path)) {
                $hero_image_path = '/assets/images/' . $file_name;
                if ($settingModel->updateSetting('hero_image', $hero_image_path)) {
                    $message = 'Hero image uploaded and settings saved!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to save hero image path to database.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Failed to move uploaded file.';
                $messageType = 'error';
            }
        } else {
            $message = 'Invalid file type. Please upload a PNG, JPG, or GIF.';
            $messageType = 'error';
        }
    }

    // Handle Contact Form Settings
    if (isset($_POST['contact_form_settings_submit'])) {
        $recipientEmail = $_POST['contact_recipient_email'] ?? '';
        $fromEmail = $_POST['contact_from_email'] ?? '';
        $fromName = $_POST['contact_from_name'] ?? '';

        $updated = 0;
        if ($settingModel->updateSetting('contact_recipient_email', $recipientEmail)) $updated++;
        if ($settingModel->updateSetting('contact_from_email', $fromEmail)) $updated++;
        if ($settingModel->updateSetting('contact_from_name', $fromName)) $updated++;

        if ($updated > 0) {
            $message = 'Contact form settings updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to update contact form settings.';
            $messageType = 'error';
        }
    }
}

$settings = $settingModel->getAllSettings();
$tinymceApiKey = $settings['tinymce_api_key'] ?? '';
$siteLogo = $settings['site_logo'] ?? '/assets/images/default-logo.png';
$heroImage = $settings['hero_image'] ?? '/assets/images/default-hero.jpg';

// These assignments must happen AFTER all POST handling
$contactRecipientEmail = $settings['contact_recipient_email'] ?? '';
$contactFromEmail = $settings['contact_from_email'] ?? '';
$contactFromName = $settings['contact_from_name'] ?? '';

$page_title = 'System Settings';
include __DIR__ . '/../header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
        <p class="text-gray-500">Manage global site settings.</p>
    </div>
</header>

<?php if ($message): ?>
    <div class="mb-4 p-3 rounded-lg text-sm <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div>
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">API Keys</h2>
            <form method="post">
                <div class="mb-6">
                    <label for="tinymce_api_key" class="block text-gray-700 text-sm font-bold mb-2">TinyMCE API Key</label>
                    <input type="text" id="tinymce_api_key" name="tinymce_api_key" value="<?= htmlspecialchars($tinymceApiKey) ?>" class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-600 text-xs italic mt-2">
                        Get your free API key from <a href="https://www.tiny.cloud/auth/signup/" target="_blank" class="text-blue-500 hover:underline">tiny.cloud</a>.
                    </p>
                </div>
                <div class="flex items-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save API Key
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Contact Form Settings</h2>
            <form method="post">
                <input type="hidden" name="contact_form_settings_submit" value="1">
                <div class="mb-6">
                    <label for="contact_recipient_email" class="block text-gray-700 text-sm font-bold mb-2">Recipient Email</label>
                    <input type="email" id="contact_recipient_email" name="contact_recipient_email" value="<?= htmlspecialchars($contactRecipientEmail) ?>" class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-600 text-xs italic mt-2">
                        The email address where contact form submissions will be sent.
                    </p>
                </div>
                <div class="mb-6">
                    <label for="contact_from_email" class="block text-gray-700 text-sm font-bold mb-2">"From" Email Address</label>
                    <input type="email" id="contact_from_email" name="contact_from_email" value="<?= htmlspecialchars($contactFromEmail) ?>" class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-600 text-xs italic mt-2">
                        The email address used in the "From" field of the sent email (often a server-mandated address like noreply@yourdomain.com).
                    </p>
                </div>
                <div class="mb-6">
                    <label for="contact_from_name" class="block text-gray-700 text-sm font-bold mb-2">"From" Display Name</label>
                    <input type="text" id="contact_from_name" name="contact_from_name" value="<?= htmlspecialchars($contactFromName) ?>" class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-600 text-xs italic mt-2">
                        The name displayed as the sender of the contact form email.
                    </p>
                </div>
                <div class="flex items-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save Contact Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Site Branding</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-6">
                <label for="site_logo" class="block text-gray-700 text-sm font-bold mb-2">Upload New Logo</label>
                <input type="file" id="site_logo" name="site_logo" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                <p class="text-gray-600 text-xs italic mt-2">
                    Recommended size: 200x50 pixels. Allowed types: PNG, JPG, GIF, SVG.
                </p>
            </div>
            <div class="mb-6">
                <p class="block text-gray-700 text-sm font-bold mb-2">Current Logo:</p>
                <img src="<?= htmlspecialchars($siteLogo) ?>" alt="Current Site Logo" class="max-h-16 border p-2 rounded-md">
            </div>
            <div class="flex items-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Upload Logo
                </button>
            </div>
        </form>
        <hr class="my-8">
        <form method="post" enctype="multipart/form-data">
            <div class="mb-6">
                <label for="hero_image" class="block text-gray-700 text-sm font-bold mb-2">Upload New Hero Image</label>
                <input type="file" id="hero_image" name="hero_image" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                <p class="text-gray-600 text-xs italic mt-2">
                    Recommended size: 800x1000 pixels. Allowed types: PNG, JPG, GIF.
                </p>
            </div>
            <div class="mb-6">
                <p class="block text-gray-700 text-sm font-bold mb-2">Current Hero Image:</p>
                <img src="<?= htmlspecialchars($heroImage) ?>" alt="Current Hero Image" class="w-full max-w-xs border p-2 rounded-md">
            </div>
            <div class="flex items-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Upload Hero Image
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>