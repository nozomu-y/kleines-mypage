<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isCamp())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "合宿集金リスト";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">合宿集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">合宿集金リスト一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金リストの追加</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form method="post" action="./add_accounting_list.php" class="mb-4">
                    <div class="form-group">
                        <label for="fee-list">集金リスト名</label>
                        <input type="text" class="form-control" name="name" id="fee-list" aria-describedby="nameHelp" required>
                        <small id="nameHelp" class="form-text text-muted">「全国大会集金」のように入力してください。</small>
                    </div>
                    <div class="form-group">
                        <label for="deadline">期限</label>
                        <input type="date" name="deadline" class="form-control" id="deadline" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">リストを追加</button>
                    <a class="btn btn-secondary" href="../" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
include_once __DIR__ . '/../../../Common/foot.php';
