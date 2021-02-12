<?php
require __DIR__ . '/../../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

$PAGE_NAME = "個別会計管理";
include_once __DIR__ . '/../../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">個別会計管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../../">個別会計管理</a></li>
                    <li class="breadcrumb-item"><a href="../">個別会計一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page">新規個別会計の追加</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form action="add_list.php" method="post">
                    <div class="form-group">
                        <label for="name">項目名</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= $name ?>" required>
                    </div>
                    <input type="hidden" name="list_id" value="<?= $list_id ?>">
                    <button type="submit" class="btn btn-primary" name="submit">追加する</button>
                    <a class="btn btn-secondary" href="../" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>




<?php
include_once __DIR__ . '/../../../../Common/foot.php';
