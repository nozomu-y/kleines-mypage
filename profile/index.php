<?php
require __DIR__ . '/../Common/init_page.php';

$PAGE_NAME = "プロフィール";
include_once __DIR__ . '/../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800">プロフィール</h1>
    <?php echo $USER->get_name(); ?>
</div>

<?php
include_once __DIR__ . '/../Common/foot.php';
