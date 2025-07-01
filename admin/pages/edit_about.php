<?php
// Generate a unique nonce for each request
$nonce = base64_encode(random_bytes(16));
$page_title = "Edit About Page";
include __DIR__ . '/../header.php';

use App\Models\Page;

$pageModel = $container->getPageModel();
$page = $pageModel->findBySlug('about');
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_data = [
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
    ];

    if ($pageModel->update($page['id'], $updated_data)) {
        $success_message = "About page updated successfully!";
        $page = $pageModel->findBySlug('about'); // Re-fetch to show updated data
    } else {
        $errors[] = "Failed to update the About page.";
    }
}
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Edit About Page</h1>
        <p class="text-gray-500">Update the content for the public-facing About Us page.</p>
    </div>
</header>

<div class="bg-white p-6 rounded-lg shadow">
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

    <form method="POST" action="edit_about.php">
        <!-- Hero Section -->
        <div class="mb-6 border-b pb-6">
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

        <!-- Mission Section -->
        <div class="mb-6 border-b pb-6">
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
                <button type="button" id="mission_image_button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Choose File
                </button>
                <input type="file" id="about_mission_image_upload" class="hidden">
                <input type="hidden" id="about_mission_image" name="about_mission_image" value="<?php echo htmlspecialchars($page['about_mission_image'] ?? ''); ?>">
                <p class="text-gray-600 text-xs mt-1">Recommended size: 800x600px</p>
                <img id="mission_image_preview" src="<?php echo htmlspecialchars($page['about_mission_image'] ?? ''); ?>" alt="Mission Image Preview" class="mt-4 max-w-xs rounded shadow" style="max-height: 150px; <?php echo empty($page['about_mission_image']) ? 'display:none;' : ''; ?>">
            </div>
        </div>

        <!-- Founder Section -->
        <div class="mb-6">
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
                <button type="button" id="founder_image_button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Choose File
                </button>
                <input type="file" id="about_founder_image_upload" class="hidden">
                <input type="hidden" id="about_founder_image" name="about_founder_image" value="<?php echo htmlspecialchars($page['about_founder_image'] ?? ''); ?>">
                <p class="text-gray-600 text-xs mt-1">Recommended size: 400x400px</p>
                <img id="founder_image_preview" src="<?php echo htmlspecialchars($page['about_founder_image'] ?? ''); ?>" alt="Founder Image Preview" class="mt-4 max-w-xs shadow" style="max-height: 150px; <?php echo empty($page['about_founder_image']) ? 'display:none;' : ''; ?>">
            </div>
        </div>

        <div class="flex items-center justify-end mt-8">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Save Changes
            </button>
        </div>
    </form>
</div>

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
            aspectRatio: 1 / 1 // Square for founder image
        },
        'about_mission_image_upload': {
            hiddenInputId: 'about_mission_image',
            previewId: 'mission_image_preview',
            aspectRatio: 4 / 3 // 4:3 for mission image
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
        }, 200); // 200ms delay
    }

    function hideModal() {
        modal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
        }
    }

    document.getElementById('mission_image_button').addEventListener('click', () => {
        document.getElementById('about_mission_image_upload').click();
    });

    document.getElementById('founder_image_button').addEventListener('click', () => {
        document.getElementById('about_founder_image_upload').click();
    });

    Object.keys(uploadConfigs).forEach(uploadInputId => {
        const uploadInput = document.getElementById(uploadInputId);
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
    });

    cancelCropBtn.addEventListener('click', hideModal);

    confirmCropBtn.addEventListener('click', function() {
        if (cropper) {
            cropper.getCroppedCanvas().toBlob(function(blob) {
                const formData = new FormData();
                formData.append('image', blob, 'cropped.png'); // Use a consistent filename

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../upload_image.php', true);

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
