</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-menu-overlay');

        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('opacity-0');
            overlay.classList.toggle('opacity-50');
            overlay.classList.toggle('pointer-events-none');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            overlay.classList.remove('opacity-50');
            overlay.classList.add('pointer-events-none');
        });
    });
</script>
</body>
</html>
