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

$user_id = $_GET['id'];
$accounting_id = $_GET['fee_id'];

$query = "SELECT *, (SELECT CONCAT(grade,part,' ',last_name,first_name) FROM profiles WHERE user_id=accounting_records.user_id) AS user_name FROM accounting_records INNER JOIN accounting_lists ON accounting_records.accounting_id=accounting_lists.accounting_id WHERE accounting_records.accounting_id=$accounting_id AND user_id=$user_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $accounting_price = $row['price'];
    $accounting_paid_cash = $row['paid_cash'];
    $accounting_datetime = $row['datetime'];
    $accounting_name = $row['name'];
    $accounting_deadline = $row['deadline'];
    $accounting_admin = $row['admin'];
    $name = $row['user_name'];
}

if ($accounting_admin != 'GENERAL') {
    header('Location:  ' . MYPAGE_ROOT . '/admin/accounting/');
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
                    <li class="breadcrumb-item"><a href="../detail.php?fee_id=<?= $accounting_id ?>"><?= $accounting_name ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">金額の変更（<?= $name ?>）</li>
                </ol>
            </nav>
            <div class="mb-4">
                <?php
                if ($accounting_datetime == NULL) {
                ?>
                    <form method="post" action="./change_price.php" class="mb-4">
                        <div class="form-group">
                            <label for="fee-list">集金リスト名</label>
                            <input type="text" class="form-control" name="name" id="fee-list" aria-describedby="nameHelp" value="<?= $accounting_name ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="deadline">期限</label>
                            <input type="date" name="deadline" class="form-control" id="deadline" value="<?= date('Y-m-d', strtotime($accounting_deadline)) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="price">金額</label>
                            <input type="number" name="price" class="form-control" id="price" value="<?= $accounting_price ?>" required>
                        </div>
                        <input type="hidden" name="fee_id" value="<?= $accounting_id ?>">
                        <input type="hidden" name="account_id" value="<?= $user_id ?>">
                        <button type="submit" class="btn btn-primary" name="submit">金額を変更</button>
                        <a class="btn btn-secondary" href="../detail.php?fee_id=<?= $accounting_id ?>" role="button">キャンセル</a>
                    </form>
                <?php
                } else {
                ?>
                    <div class="alert alert-info shadow p-3 mb-4" role="alert">既に集金が完了しています。</div>
                    <a class="btn btn-secondary" href="../detail.php?fee_id=<?= $accounting_id ?>" role="button">キャンセル</a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>


<?php
include_once __DIR__ . '/../../../Common/foot.php';
