<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "個別会計管理";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">個別会計管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">個別会計管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page">項目の追加</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form action="subject.php" method="post">
                    <div class="form-group">
                        <label for="date">日付</label>
                        <input type="date" name="date" class="form-control" id="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="name">項目名</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">金額</label>
                        <input type="number" name="price" id="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">追加する</button>
                    <a class="btn btn-secondary" href="../" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>




<?php
include_once __DIR__ . '/../../../Common/foot.php';
