<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_GET['fee_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$accounting_id = $_GET['fee_id'];
$accounting = new AccountingList($accounting_id);

if ($accounting->admin != 'GENERAL') {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}
$PAGE_NAME = "集金リスト";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">集金リスト一覧</a></li>
                    <li class="breadcrumb-item"><a href="../detail.php?fee_id=<?= $accounting->accounting_id ?>"><?= $accounting->name ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金リストの編集</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form method="post" action="./edit_accounting_list.php" class="mb-4">
                    <div class="form-group">
                        <label for="fee-list">集金リスト名</label>
                        <input type="text" class="form-control" name="name" id="fee-list" aria-describedby="nameHelp" value="<?= $accounting->name ?>" required>
                        <small id="nameHelp" class="form-text text-muted">「団費集金」「演奏会費集金」のように入力してください。</small>
                    </div>
                    <div class="form-group">
                        <label for="deadline">期限</label>
                        <input type="date" name="deadline" class="form-control" id="deadline" value="<?= date('Y-m-d', strtotime($accounting->deadline)) ?>" required>
                    </div>
                    <input type="hidden" name="fee_id" value="<?= $accounting->accounting_id ?>">
                    <button type="submit" class="btn btn-primary" name="submit">リストを更新</button>
                    <a class="btn btn-secondary" href="../detail.php?fee_id=<?= $accounting->accounting_id ?>" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
include_once __DIR__ . '/../../../Common/foot.php';
