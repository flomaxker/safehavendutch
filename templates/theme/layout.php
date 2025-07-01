
<?php include 'header.php'; ?>

<div class="main-content">
    <?php 
    if (isset($page_content_render_function) && function_exists($page_content_render_function)) {
        $page_content_render_function($page_content, $latest_posts ?? []);
    } else {
        echo $page_content;
    }
    ?>
</div>

<?php include 'footer.php'; ?>
