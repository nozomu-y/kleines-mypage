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

if (!($user->admin == 1 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

if (!isset($_POST['delete'])) {
    header('Location: /member/mypage/admin/individual_accounting/');
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

error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "の個別会計データ「" . $individual->name . "」を削除しました。\n", 3, "/home/chorkleines/www/member/mypage/Core/individual_accounting.log");
header('Location: detail.php?account_id=' . $account->id);
exit();
