<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['fee_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$fee_id = $_POST['fee_id'];
$query = "SELECT * FROM fee_list WHERE id='$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee_list = new Fee_List($result->fetch_assoc());
if ($fee_list->admin != 3) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$user_id = $_POST['user_id'];

$query = "SELECT * FROM members WHERE id='$user_id'";
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
if ($fee->paid_individual != 0) {
    $query = "DELETE FROM individual_accounting_$account->id WHERE fee_id = $fee_list->id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
}

$query = "UPDATE fee_record_$account->id SET datetime = NULL, paid_cash = NULL WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}


/** ログファイル作成の処理 **/
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . "が" . $account->name . "の「" . $fee_list->name . "」の提出状況を未納に変更しました。\n", 3, __DIR__ . "/../../Core/accounting.log");

$_SESSION['mypage_account_name'] = $account->get_name();
$_SESSION['mypage_fee_status'] = "未納";

header('Location: ' . MYPAGE_ROOT . '/admin/accounting/detail.php?fee_id=' . $fee_list->id);
exit();
