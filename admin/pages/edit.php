<?php
$page_title = "Edit Page";
include __DIR__ . '/../header.php';

use App\Models\Page;

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
    ];

    // Basic validation
    if (empty($updated_data['title'])) {
        $errors[] = "Title is required.";
    }
    if (empty($updated_data['slug'])) {
        $errors[] = "Slug is required.";
    }

    if (empty($errors)) {
        if ($pageModel->update($page_id, $updated_data)) {
            $success_message = "Page updated successfully!";
            // Re-fetch the page to show updated data
            $page = $pageModel->findById($page_id);
        } else {
            $errors[] = "Failed to update page.";
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors[] = "Invalid request.";
}

?>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#main_content',
        plugins: 'autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        height: '500px',
        // The following options are to ensure that the editor renders the HTML visually
        // and does not strip out any tags or attributes.
        verify_html: false,
        cleanup: false,
        forced_root_block: '',
    });
</script>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Edit Page</h1>
        <p class="text-gray-500">Modify the content and metadata for this page.</p>
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

    <?php if ($page): ?>
        <form method="POST" action="edit.php?id=<?php echo $page['id']; ?>">
            <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($page['id']); ?>">

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

            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Hero Section</h2>
                <div class="mb-4">
                    <label for="hero_title" class="block text-gray-700 text-sm font-bold mb-2">Hero Title:</label>
                    <input type="text" id="hero_title" name="hero_title" value="<?php echo htmlspecialchars($page['hero_title'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4">
                    <label for="hero_subtitle" class="block text-gray-700 text-sm font-bold mb-2">Hero Subtitle:</label>
                    <textarea id="hero_subtitle" name="hero_subtitle" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24"><?php echo htmlspecialchars($page['hero_subtitle'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Main Content</h2>
                <textarea id="main_content" name="main_content" class="h-64"><?php echo $page['main_content'] ?? ''; ?></textarea>
            </div>

            <div class="mt-6 border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Page Components</h2>
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="show_contact_form" name="show_contact_form" value="1" <?php echo !empty($page['show_contact_form']) ? 'checked' : ''; ?>>
                    <label for="show_contact_form" class="ml-2 text-gray-700">Show Contact Form</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="show_packages" name="show_packages" value="1" <?php echo !empty($page['show_packages']) ? 'checked' : ''; ?>>
                    <label for="show_packages" class="ml-2 text-gray-700">Show Packages/Pricing Grid</label>
                </div>
            </div>

            <div class="mt-6 border-t pt-6">
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
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Page
                </button>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Cancel
                </a>
            </div>
        </form>
    <?php else: ?>
        <p class="text-gray-700">Page not found or invalid ID.</p>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4 inline-block">
            Back to Pages
        </a>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../footer.php';
?>
