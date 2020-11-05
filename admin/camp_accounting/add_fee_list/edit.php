<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 5)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_GET['fee_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

$fee_id = $_GET['fee_id'];
$query = "SELECT * FROM fee_list WHERE id='$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee_list = new Fee_List($result->fetch_assoc());
if ($fee_list->admin != 5) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}
$PAGE_NAME = "合宿集金";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">合宿集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">合宿集金リスト一覧</a></li>
                    <li class="breadcrumb-item"><a href="../detail.php?fee_id=<?php echo $fee_list->id ?>"><?php echo $fee_list->name ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金リストの編集</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form method="post" action="./edit_fee_list.php" class="mb-4">
                    <div class="form-group">
                        <label for="fee-list">集金リスト名</label>
                        <input type="text" class="form-control" name="name" id="fee-list" aria-describedby="nameHelp" value="<?php echo $fee_list->name; ?>" required>
                        <small id="nameHelp" class="form-text text-muted">「全国大会費」のように入力してください。</small>
                    </div>
                    <div class="form-group">
                        <label for="deadline">期限</label>
                        <input type="date" name="deadline" class="form-control" id="deadline" value="<?php echo date('Y-m-d', strtotime($fee_list->deadline)); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">金額</label>
                        <input type="number" name="price" class="form-control" id="price" value="<?php echo $fee_list->price; ?>" readonly>
                        <small id="nameHelp" class="form-text text-muted">金額は一括変更できません。</small>
                    </div>
                    <input type="hidden" name="fee_id" value="<?php echo $fee_list->id; ?>">
                    <button type="submit" class="btn btn-primary" name="submit">リストを更新</button>
                    <a class="btn btn-secondary" href="../detail.php?fee_id=<?php echo $fee_list->id ?>" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
include_once __DIR__ . '/../../../Common/foot.php';
