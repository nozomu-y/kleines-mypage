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

$name = $mysqli->real_escape_string($_POST['name']);
$deadline = $mysqli->real_escape_string($_POST['deadline']);
$price = $mysqli->real_escape_string($_POST['price']);

$query = "SELECT id FROM fee_list ORDER BY id ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $fee_id = $row['id'];
}
$fee_admin = 5;
$fee_id = $fee_id + 1;
$query = "INSERT INTO fee_list (id, name, deadline, price, admin) VALUES ('$fee_id', '$name', '$deadline', '$price', '$fee_admin')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が新規集金リスト「" . $name . "」を追加しました。（期限：" . $deadline . "、金額：" . $price . "）\n", 3, "/home/chorkleines/www/member/mypage/Core/camp_accounting.log");
header('Location: subject.php?fee_id=' . $fee_id);
exit();
