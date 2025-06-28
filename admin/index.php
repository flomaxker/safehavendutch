<?php
$page_title = "Dashboard";
include __DIR__ . '/header.php';
?>

<header class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Welcome to the Admin Dashboard</h1>
        <p class="text-gray-500">Manage your website content here.</p>
    </div>
</header>

<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-blue-50 p-6 rounded-xl flex items-start">
        <div class="bg-blue-100 p-2 rounded-lg mr-4">
            <span class="material-icons text-blue-600">description</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Pages</p>
            <p class="text-2xl font-bold text-gray-800">5</p> <!-- Placeholder, will be dynamic -->
        </div>
    </div>
    <div class="bg-green-50 p-6 rounded-xl flex items-start">
        <div class="bg-green-100 p-2 rounded-lg mr-4">
            <span class="material-icons text-green-600">edit</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Last Updated Page</p>
            <p class="text-lg font-bold text-gray-800">Homepage</p> <!-- Placeholder, will be dynamic -->
        </div>
    </div>
    <div class="bg-yellow-50 p-6 rounded-xl flex items-start">
        <div class="bg-yellow-100 p-2 rounded-lg mr-4">
            <span class="material-icons text-yellow-600">visibility</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Website Views (Placeholder)</p>
            <p class="text-2xl font-bold text-gray-800">1,234</p>
        </div>
    </div>
</section>

<section>
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="pages/index.php" class="bg-gray-100 p-6 rounded-xl flex items-center hover:bg-gray-200 transition">
            <span class="material-icons text-gray-600 mr-4">list_alt</span>
            <span class="text-lg font-medium text-gray-800">View All Pages</span>
        </a>
        <!-- Add more quick actions as needed -->
    </div>
</section>

<?php
include __DIR__ . '/footer.php';
?>