<?php
$page_title = "Edit Page";
include __DIR__ . '/../header.php';

use App\Models\Page;
use App\Models\Package;

$pageModel = $container->getPageModel();
$page = null;
$errors = [];
$success_message = '';

if (isset($_GET['id'])) {
    $page = $pageModel->findById((int)$_GET['id']);
    if (!$page) {
        $errors[] = "Page not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['page_id'])) {
    $page_id = (int)$_POST['page_id'];
    $updated_data = [
        'title' => $_POST['title'] ?? '',
        'slug' => $_POST['slug'] ?? '',
        'meta_description' => $_POST['meta_description'] ?? '',
        'og_title' => $_POST['og_title'] ?? '',
        'og_description' => $_POST['og_description'] ?? '',
        'og_url' => $_POST['og_url'] ?? '',
        'og_image' => $_POST['og_image'] ?? '',
        'hero_title' => $_POST['hero_title'] ?? '',
        'hero_subtitle' => $_POST['hero_subtitle'] ?? '',
        'main_content' => $_POST['main_content'] ?? '',
        'show_contact_form' => isset($_POST['show_contact_form']) ? 1 : 0,
        'show_packages' => isset($_POST['show_packages']) ? 1 : 0,
        'page_type' => $_POST['page_type'] ?? 'standard',
        'about_hero_title' => $_POST['about_hero_title'] ?? '',
        'about_hero_subtitle' => $_POST['about_hero_subtitle'] ?? '',
        'about_mission_heading' => $_POST['about_mission_heading'] ?? '',
        'about_mission_text' => $_POST['about_mission_text'] ?? '',
        'about_mission_image' => $_POST['about_mission_image'] ?? '',
        'about_founder_heading' => $_POST['about_founder_heading'] ?? '',
        'about_founder_name' => $_POST['about_founder_name'] ?? '',
        'about_founder_title' => $_POST['about_founder_title'] ?? '',
        'about_founder_quote' => $_POST['about_founder_quote'] ?? '',
        'about_founder_image' => $_POST['about_founder_image'] ?? '',
        'features_heading' => $_POST['features_heading'] ?? null,
        'feature1_icon' => $_POST['feature1_icon'] ?? null,
        'feature1_title' => $_POST['feature1_title'] ?? null,
        'feature1_description' => $_POST['feature1_description'] ?? null,
        'feature2_icon' => $_POST['feature2_icon'] ?? null,
        'feature2_title' => $_POST['feature2_title'] ?? null,
        'feature2_description' => $_POST['feature2_description'] ?? null,
        'feature3_icon' => $_POST['feature3_icon'] ?? null,
        'feature3_title' => $_POST['feature3_title'] ?? null,
        'feature3_description' => $_POST['feature3_description'] ?? null,
    ];

    if (empty($updated_data['title'])) {
        $errors[] = "Title is required.";
    }
    if (empty($updated_data['slug'])) {
        $errors[] = "Slug is required.";
    }

    if (empty($errors)) {
        if ($pageModel->update($page_id, $updated_data)) {
            $success_message = "Page updated successfully!";
            $page = $pageModel->findById($page_id);
        } else {
            $errors[] = "Failed to update page.";
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors[] = "Invalid request.";
}

$_SESSION['csrf_token'] = $nonce; // Use the single, valid nonce from the header for security.
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Edit Page</h1>
        <p class="text-gray-500">Modify the content and metadata for this page.</p>
    </div>
</header>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <?php foreach ($errors as $error): ?>
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success_message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo htmlspecialchars($success_message); ?></span>
    </div>
<?php endif; ?>

<?php if ($page): ?>
    <form method="POST" action="edit.php?id=<?php echo $page['id']; ?>">
        <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($page['id']); ?>">
        <input type="hidden" name="page_type" value="<?php echo htmlspecialchars($page['page_type']); ?>">

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Page Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($page['title']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div>
                    <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">Slug:</label>
                    <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($page['slug']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
            </div>

            <?php if ($page['page_type'] === 'home'): ?>
            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Hero Section</h2>
                <div class="mb-4">
                    <label for="hero_title" class="block text-gray-700 text-sm font-bold mb-2">Hero Title:</label>
                    <input type="text" id="hero_title" name="hero_title" value="<?php echo htmlspecialchars($page['hero_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="hero_subtitle" class="block text-gray-700 text-sm font-bold mb-2">Hero Subtitle:</label>
                    <textarea id="hero_subtitle" name="hero_subtitle" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 h-24"><?php echo htmlspecialchars($page['hero_subtitle'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Features Section</h2>
                <div class="mb-4">
                    <label for="features_heading" class="block text-gray-700 text-sm font-bold mb-2">Features Heading:</label>
                    <input type="text" id="features_heading" name="features_heading" value="<?php echo htmlspecialchars($page['features_heading'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="feature1_icon" class="block text-gray-700 text-sm font-bold mb-2">Feature 1 Icon:</label>
                        <input type="text" id="feature1_icon" name="feature1_icon" value="<?php echo htmlspecialchars($page['feature1_icon'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <label for="feature1_title" class="block text-gray-700 text-sm font-bold mb-2 mt-2">Feature 1 Title:</label>
                        <input type="text" id="feature1_title" name="feature1_title" value="<?php echo htmlspecialchars($page['feature1_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <label for="feature1_description" class="block text-gray-700 text-sm font-bold mb-2 mt-2">Feature 1 Description:</label>
                        <textarea id="feature1_description" name="feature1_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 h-24"><?php echo htmlspecialchars($page['feature1_description'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label for="feature2_icon" class="block text-gray-700 text-sm font-bold mb-2">Feature 2 Icon:</label>
                        <input type="text" id="feature2_icon" name="feature2_icon" value="<?php echo htmlspecialchars($page['feature2_icon'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <label for="feature2_title" class="block text-gray-700 text-sm font-bold mb-2 mt-2">Feature 2 Title:</label>
                        <input type="text" id="feature2_title" name="feature2_title" value="<?php echo htmlspecialchars($page['feature2_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <label for="feature2_description" class="block text-gray-700 text-sm font-bold mb-2 mt-2">Feature 2 Description:</label>
                        <textarea id="feature2_description" name="feature2_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 h-24"><?php echo htmlspecialchars($page['feature2_description'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label for="feature3_icon" class="block text-gray-700 text-sm font-bold mb-2">Feature 3 Icon:</label>
                        <input type="text" id="feature3_icon" name="feature3_icon" value="<?php echo htmlspecialchars($page['feature3_icon'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <label for="feature3_title" class="block text-gray-700 text-sm font-bold mb-2 mt-2">Feature 3 Title:</label>
                        <input type="text" id="feature3_title" name="feature3_title" value="<?php echo htmlspecialchars($page['feature3_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <label for="feature3_description" class="block text-gray-700 text-sm font-bold mb-2 mt-2">Feature 3 Description:</label>
                        <textarea id="feature3_description" name="feature3_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 h-24"><?php echo htmlspecialchars($page['feature3_description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($page['page_type'] === 'standard'): ?>
            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Main Content</h2>
                <textarea id="main_content" name="main_content" class="h-64"><?php echo $page['main_content'] ?? ''; ?></textarea>
            </div>
            <?php endif; ?>

            <?php if ($page['page_type'] === 'packages'): ?>
            <!-- Packages Page Fields -->
            <?php endif; ?>

            <?php if ($page['page_type'] === 'about'): ?>
            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Hero Section</h2>
                <div class="mb-4">
                    <label for="about_hero_title" class="block text-gray-700 text-sm font-bold mb-2">Hero Title:</label>
                    <input type="text" id="about_hero_title" name="about_hero_title" value="<?php echo htmlspecialchars($page['about_hero_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="about_hero_subtitle" class="block text-gray-700 text-sm font-bold mb-2">Hero Subtitle:</label>
                    <textarea id="about_hero_subtitle" name="about_hero_subtitle" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 h-24"><?php echo htmlspecialchars($page['about_hero_subtitle'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Mission Section</h2>
                <div class="mb-4">
                    <label for="about_mission_heading" class="block text-gray-700 text-sm font-bold mb-2">Mission Heading:</label>
                    <input type="text" id="about_mission_heading" name="about_mission_heading" value="<?php echo htmlspecialchars($page['about_mission_heading'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="about_mission_text" class="block text-gray-700 text-sm font-bold mb-2">Mission Text:</label>
                    <textarea id="about_mission_text" name="about_mission_text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 h-32"><?php echo htmlspecialchars($page['about_mission_text'] ?? ''); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="about_mission_image_upload" class="block text-gray-700 text-sm font-bold mb-2">Mission Image:</label>
                    <button type="button" id="mission_image_button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Choose File</button>
                    <input type="file" id="about_mission_image_upload" class="hidden">
                    <input type="hidden" id="about_mission_image" name="about_mission_image" value="<?php echo htmlspecialchars($page['about_mission_image'] ?? ''); ?>">
                    <p class="text-gray-600 text-xs mt-1">Recommended size: 800x600px</p>
                    <img id="mission_image_preview" src="<?php echo htmlspecialchars($page['about_mission_image'] ?? ''); ?>" alt="Mission Image Preview" class="mt-4 max-w-xs rounded shadow" style="max-height: 150px; <?php echo empty($page['about_mission_image']) ? 'display:none;' : ''; ?>">
                </div>
            </div>
            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Founder Section</h2>
                <div class="mb-4">
                    <label for="about_founder_heading" class="block text-gray-700 text-sm font-bold mb-2">Founder Section Heading:</label>
                    <input type="text" id="about_founder_heading" name="about_founder_heading" value="<?php echo htmlspecialchars($page['about_founder_heading'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="about_founder_name" class="block text-gray-700 text-sm font-bold mb-2">Founder's Name:</label>
                        <input type="text" id="about_founder_name" name="about_founder_name" value="<?php echo htmlspecialchars($page['about_founder_name'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label for="about_founder_title" class="block text-gray-700 text-sm font-bold mb-2">Founder's Title:</label>
                        <input type="text" id="about_founder_title" name="about_founder_title" value="<?php echo htmlspecialchars($page['about_founder_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="about_founder_quote" class="block text-gray-700 text-sm font-bold mb-2">Founder's Quote:</label>
                    <textarea id="about_founder_quote" name="about_founder_quote" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 h-24"><?php echo htmlspecialchars($page['about_founder_quote'] ?? ''); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="about_founder_image_upload" class="block text-gray-700 text-sm font-bold mb-2">Founder's Image:</label>
                    <button type="button" id="founder_image_button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Choose File</button>
                    <input type="file" id="about_founder_image_upload" class="hidden">
                    <input type="hidden" id="about_founder_image" name="about_founder_image" value="<?php echo htmlspecialchars($page['about_founder_image'] ?? ''); ?>">
                    <p class="text-gray-600 text-xs mt-1">Recommended size: 400x400px</p>
                    <img id="founder_image_preview" src="<?php echo htmlspecialchars($page['about_founder_image'] ?? ''); ?>" alt="Founder Image Preview" class="mt-4 max-w-xs shadow" style="max-height: 150px; <?php echo empty($page['about_founder_image']) ? 'display:none;' : ''; ?>">
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-lg shadow mt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">SEO & Metadata</h2>
            <div class="mb-4">
                <label for="meta_description" class="block text-gray-700 text-sm font-bold mb-2">Meta Description:</label>
                <textarea id="meta_description" name="meta_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24"><?php echo htmlspecialchars($page['meta_description'] ?? ''); ?></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="og_title" class="block text-gray-700 text-sm font-bold mb-2">OG Title:</label>
                    <input type="text" id="og_title" name="og_title" value="<?php echo htmlspecialchars($page['og_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label for="og_url" class="block text-gray-700 text-sm font-bold mb-2">OG URL:</label>
                    <input type="text" id="og_url" name="og_url" value="<?php echo htmlspecialchars($page['og_url'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
            <div class="mb-4 mt-4">
                <label for="og_description" class="block text-gray-700 text-sm font-bold mb-2">OG Description:</label>
                <textarea id="og_description" name="og_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24"><?php echo htmlspecialchars($page['og_description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="og_image" class="block text-gray-700 text-sm font-bold mb-2">OG Image URL:</label>
                <input type="text" id="og_image" name="og_image" value="<?php echo htmlspecialchars($page['og_image'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>

        <div class="flex items-center justify-between mt-8">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Page</button>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancel</a>
        </div>
    </form>
<?php else: ?>
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-700">Page not found or invalid ID.</p>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4 inline-block">Back to Pages</a>
    </div>
<?php endif; ?>

<div id="cropping-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl">
        <h2 class="text-2xl font-bold mb-4">Crop Image</h2>
        <div>
            <img id="image-to-crop" src="" alt="Image to crop">
        </div>
        <div class="mt-4 flex justify-end">
            <button id="cancel-crop" type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Cancel</button>
            <button id="confirm-crop" type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Crop & Upload</button>
        </div>
    </div>
</div>

<script nonce="<?php echo $nonce; ?>">
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cropping-modal');
    const imageToCrop = document.getElementById('image-to-crop');
    const confirmCropBtn = document.getElementById('confirm-crop');
    const cancelCropBtn = document.getElementById('cancel-crop');
    let cropper;
    let currentUploadConfig;

    const uploadConfigs = {
        'about_founder_image_upload': {
            hiddenInputId: 'about_founder_image',
            previewId: 'founder_image_preview',
            aspectRatio: 1 / 1
        },
        'about_mission_image_upload': {
            hiddenInputId: 'about_mission_image',
            previewId: 'mission_image_preview',
            aspectRatio: 4 / 3
        }
    };

    function showModal(imageSrc, config) {
        currentUploadConfig = config;
        imageToCrop.src = imageSrc;
        modal.classList.remove('hidden');
        setTimeout(() => {
            cropper = new Cropper(imageToCrop, {
                aspectRatio: config.aspectRatio,
                viewMode: 1,
            });
        }, 200);
    }

    function hideModal() {
        modal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
        }
    }

    document.getElementById('mission_image_button')?.addEventListener('click', () => {
        document.getElementById('about_mission_image_upload').click();
    });

    document.getElementById('founder_image_button')?.addEventListener('click', () => {
        document.getElementById('about_founder_image_upload').click();
    });

    Object.keys(uploadConfigs).forEach(uploadInputId => {
        const uploadInput = document.getElementById(uploadInputId);
        if(uploadInput) {
            uploadInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        showModal(e.target.result, uploadConfigs[uploadInputId]);
                    };
                    try {
                        reader.readAsDataURL(file);
                    } catch (error) {
                        alert('Could not read the selected file. It may be corrupted or in an unsupported format.');
                        console.error('File reading error:', error);
                    }
                }
            });
        }
    });

    cancelCropBtn.addEventListener('click', hideModal);

    confirmCropBtn.addEventListener('click', function() {
        if (cropper) {
            cropper.getCroppedCanvas().toBlob(function(blob) {
                const formData = new FormData();
                formData.append('image', blob, 'cropped.png');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../upload_image.php', true);
                xhr.setRequestHeader('X-CSRF-Token', '<?php echo $nonce; ?>');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const data = JSON.parse(xhr.responseText);
                        if (data.url) {
                            document.getElementById(currentUploadConfig.hiddenInputId).value = data.url;
                            document.getElementById(currentUploadConfig.previewId).src = data.url;
                            document.getElementById(currentUploadConfig.previewId).style.display = 'block';
                            hideModal();
                        } else {
                            alert('Upload failed: ' + (data.error || 'Unknown error'));
                        }
                    } else {
                        alert('Upload failed with status: ' + xhr.status);
                    }
                };

                xhr.onerror = function() {
                    alert('An error occurred during the upload.');
                };

                xhr.send(formData);
            }, 'image/png');
        }
    });
});
</script>

<?php
include __DIR__ . '/../footer.php';
?>
