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

if (!isset($_POST['submit'])) {
    header('Location: /member/mypage/admin/camp_accounting/');
    exit();
}

$fee_id = $_POST['fee_id'];
$account_id = $_POST['account_id'];
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
    header('Location: /member/mypage/admin/camp_accounting/');
    exit();
}

$price = $mysqli->real_escape_string($_POST['price']);

$query = "UPDATE fee_record_$account_id SET price = '$price' WHERE id = '$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_update_price'] = $price;
$_SESSION['mypage_account_name'] = $account->get_name();

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "の集金リスト「" . $fee->name . "」の金額を変更しました。（金額：" . $price . "）\n", 3, "/home/chorkleines/www/member/mypage/Core/camp_accounting.log");
header('Location: /member/mypage/admin/camp_accounting/detail.php?fee_id=' . $fee_id);
exit();
