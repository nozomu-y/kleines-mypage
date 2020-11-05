<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 5)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_GET['fee_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

$account_id = $_GET['id'];
$fee_id = $_GET['fee_id'];
$query = "SELECT * FROM members WHERE id='$account_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$account = new User($result->fetch_assoc());

$query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee = new Fee($result->fetch_assoc());

if ($fee->admin != 5) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

$PAGE_NAME = "合宿集金";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">合宿集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./">合宿集金リスト一覧</a></li>
                    <li class="breadcrumb-item"><a href="./detail.php?fee_id=<?php echo $fee->id ?>"><?php echo $fee->name ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">金額の変更（<?php echo $account->get_name() ?>）</li>
                </ol>
            </nav>
            <div class="mb-4">
                <?php
                if ($fee->datetime == NULL) {
                ?>
                    <form method="post" action="./edit_price.php" class="mb-4">
                        <div class="form-group">
                            <label for="fee-list">集金リスト名</label>
                            <input type="text" class="form-control" name="name" id="fee-list" aria-describedby="nameHelp" value="<?php echo $fee->name; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="deadline">期限</label>
                            <input type="date" name="deadline" class="form-control" id="deadline" value="<?php echo date('Y-m-d', strtotime($fee->deadline)); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="price">金額</label>
                            <input type="number" name="price" class="form-control" id="price" value="<?php echo $fee->price; ?>" required>
                        </div>
                        <input type="hidden" name="fee_id" value="<?php echo $fee->id; ?>">
                        <input type="hidden" name="account_id" value="<?php echo $account->id; ?>">
                        <button type="submit" class="btn btn-primary" name="submit">金額を変更</button>
                        <a class="btn btn-secondary" href="./detail.php?fee_id=<?php echo $fee->id ?>" role="button">キャンセル</a>
                    </form>
                <?php
                } else {
                ?>
                    <div class="alert alert-info shadow p-3 mb-4" role="alert">既に集金が完了しています。</div>
                    <a class="btn btn-secondary" href="./detail.php?fee_id=<?php echo $fee->id; ?>" role="button">キャンセル</a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>


<?php
include_once __DIR__ . '/../../Common/foot.php';
