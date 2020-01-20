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

if (!($user->admin == 1)) {
    header('Location: /member/mypage/');
    exit();
}

// $query = "SELECT * FROM members ORDER BY id ASC";
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $account = new User($row);
    $query = "ALTER TABLE fee_record_$account->id ADD `fee_id` INT(3) NULL DEFAULT NULL AFTER `status`";
    $result_1 = $mysqli->query($query);
    if (!$result_1) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    echo $account->id . ' ' . $account->get_name() . '<br>';
}
