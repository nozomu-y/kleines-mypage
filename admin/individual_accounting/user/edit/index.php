<?php
require __DIR__ . '/../../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_GET['user_id']) && isset($_GET['list_id'])) {
    $user_id = $_GET['user_id'];
    $list_id = $_GET['list_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

$account = new User($user_id);

$query = "SELECT individual_accounting_lists.name, individual_accounting_records.list_id, individual_accounting_records.price, individual_accounting_records.datetime FROM individual_accounting_records INNER JOIN individual_accounting_lists ON individual_accounting_records.list_id=individual_accounting_lists.list_id WHERE individual_accounting_records.user_id=$user_id AND individual_accounting_records.list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $date = date('Y-m-d', strtotime($row['datetime']));
    $price = $row['price'];
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
                    <li class="breadcrumb-item"><a href="../?user_id=<?= $user_id ?>"><?= $account->get_name() ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $name ?></li>
                </ol>
            </nav>
            <div class="mb-4">
                <form action="edit.php" method="post">
                    <div class="form-group">
                        <label for="name">項目名</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= $name ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="date">日付</label>
                        <input type="date" name="date" class="form-control" id="date" aria-describedby="dateHelp" value="<?= $date ?>" readonly>
                        <small id="dateHelp" class="form-text text-muted">金額を更新しても日付は登録された日付のままになります。</small>
                    </div>
                    <div class="form-group">
                        <label for="price">金額</label>
                        <input type="number" name="price" id="price" class="form-control" aria-describedby="priceHelp" value="<?= $price ?>" required>
                        <small id="priceHelp" class="form-text text-muted">個別会計から差し引く場合は負の値、個別会計に追加する場合は正の値を入力してください。</small>
                    </div>
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <input type="hidden" name="list_id" value="<?= $list_id ?>">
                    <button type="submit" class="btn btn-primary" name="submit">変更する</button>
                    <a class="btn btn-secondary" href="../?user_id=<?= $user_id ?>" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>




<?php
include_once __DIR__ . '/../../../../Common/foot.php';
