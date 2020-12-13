<?php
require __DIR__.'/Common/init_page.php';
include_once __DIR__.'/Common/head.php';
?>

<div class="container-fluid">
    <div class="text-center">
        <div class="error mx-auto" data-text="404">404</div>
        <p class="lead text-gray-800 mb-5">Page Not Found</p>
        <a href="<?=MYPAGE_ROOT?>">ホーム</a>
    </div>
</div>

<?php
include_once __DIR__.'/Common/foot.php';
