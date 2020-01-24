<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (!($user->admin == 1 || $user->admin == 5)) {
    header('Location: /member/mypage/');
    exit();
}

if (!isset($_POST['fee_id'])) {
    header('Location: /member/mypage/admin/camp_accounting/');
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
if ($fee_list->admin != 5) {
    header('Location: /member/mypage/admin/camp_accounting/');
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

$query = "UPDATE fee_record_$account->id SET datetime = NULL, paid_cash = NULL WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}


/** ログファイル作成の処理 **/
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "の「" . $fee_list->name . "」の提出状況を未納に変更しました。\n", 3, "/home/chorkleines/www/member/mypage/Core/camp_accounting.log");

$_SESSION['mypage_account_name'] = $account->get_name();
$_SESSION['mypage_fee_status'] = "未納";

header('Location: /member/mypage/admin/camp_accounting/detail.php?fee_id=' . $fee_list->id);
exit();
