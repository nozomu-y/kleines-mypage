<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['delete'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

$data = $_POST['delete'];
$data = explode('_', $data);
$account_id = $data[0];
$individual_id = $data[1];

$query = "SELECT * FROM members WHERE id=$account_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$account = new User($result->fetch_assoc());

$query = "SELECT * FROM individual_accounting_$account->id WHERE id=$individual_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$individual = new Individual_Accounting($result->fetch_assoc());

$query = "DELETE FROM individual_accounting_$account->id WHERE id = $individual_id";
$result = $mysqli->query($query);
if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_individual_delete'] = $individual->name;

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が" . $account->get_name() . "の個別会計データ「" . $individual->name . "」を削除しました。\n", 3, __DIR__ . "/../../Core/individual_accounting.log");
header('Location: detail.php?account_id=' . $account->id);
exit();
