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


if (!isset($_GET['list_id'])) {
    header('Location: index.php');
    exit();
}
$list_id = $_GET['list_id'];

$query = "SELECT * FROM ticket_list WHERE list_id = $list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $ticket_name = $row['ticket_name'];
    $date = $row['date'];
    $open_time = $row['open_time'];
    $start_time = $row['start_time'];
    $place = $row['place'];
}

echo '<h2>' . $ticket_name . '</h2>';
echo $date . '<br>';
echo '開場時間：' . $open_time . '<br>';
echo '開演時間：' . $start_time . '<br>';
echo $place;
