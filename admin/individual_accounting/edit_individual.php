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
    header('Location: /member/mypage/admin/individual_accounting/');
    exit();
}

if (isset($_POST['account_id'])) {
    $account_id = $_POST['account_id'];
} else {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$query = "SELECT * FROM members WHERE id=$account_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$account = new User($result->fetch_assoc());

if (isset($_POST['list_id'])) {
    $list_id = $_POST['list_id'];
} else {
    header('Location: /member/mypage/admin/individual_accounting/');
    exit();
}

$query = "SELECT * FROM individual_accounting_$account->id WHERE id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$individual = new Individual_Accounting($result->fetch_assoc());

$date = $_POST['date'];
$name = $_POST['name'];
$price = $_POST['price'];

$query = "UPDATE individual_accounting_$account->id SET name = '$name', date = '$date', price = '$price' WHERE id = '$individual->id'";
