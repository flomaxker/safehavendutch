<?php
require_once __DIR__ . '/bootstrap.php';
include 'header.php';
?>

<body class="bg-gray-50">
    <div class="bg-blur-circle-tl"></div>
    <div class="bg-blur-circle-br"></div>

    <section class="text-center py-20">
        <div class="container mx-auto">
            <h1 class="text-5xl font-bold text-primary-800 mb-4">Payment Successful!</h1>
            <p class="text-xl text-primary-700 max-w-3xl mx-auto mb-8">
                Thank you for your purchase. Your credits have been added to your account.
            </p>
            <a href="/dashboard.php" class="bg-primary-600 text-white px-6 py-3 rounded-md hover:bg-primary-500">
                Go to Dashboard
            </a>
        </div>
    </section>

</body>

<?php
include 'footer.php';
?>
</html>
