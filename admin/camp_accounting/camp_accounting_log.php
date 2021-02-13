<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isCamp())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "合宿集金";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">合宿集金記録</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./">合宿集金記録</a></li>
                    <li class="breadcrumb-item active" aria-current="page">ログ</li>
                </ol>
            </nav>
            <?php
            $filename = __DIR__ . "/../../Core/camp_accounting.log";
            $lines = file($filename);
            $lines = array_reverse($lines);
            foreach ($lines as $line) {
                echo nl2br($line);
            }
            ?>
        </div>
        <div class="col-xl-3 col-sm-12">

        </div>
    </div>
</div>


<?php
include_once __DIR__ . '/../../Common/foot.php';
