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

if (!isset($_POST['submit'])) {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$fee_id = $_POST['fee_id'];

$name = $mysqli->real_escape_string($_POST['name']);
$deadline = $mysqli->real_escape_string($_POST['deadline']);

$query = "UPDATE fee_list SET name = '$name', deadline = '$deadline' WHERE id = '$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_update_fee'] = '';

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が新規集金リスト「" . $name . "」を編集しました。（期限：" . $deadline . "）\n", 3, "/home/chorkleines/www/member/mypage/Core/accounting.log");
header('Location: /member/mypage/admin/accounting/detail.php?fee_id=' . $fee_id);
exit();
