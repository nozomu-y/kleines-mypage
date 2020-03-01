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

if (!isset($_POST['submit'])) {
    header('Location: /member/mypage/admin/eticket/');
    exit();
}

$ticket_name = $mysqli->real_escape_string($_POST['name']);
$date = $mysqli->real_escape_string($_POST['date']);
$open_time = $mysqli->real_escape_string($_POST['open_time']);
$start_time = $mysqli->real_escape_string($_POST['start_time']);
$place = $mysqli->real_escape_string($_POST['place']);
$ticket_price = $mysqli->real_escape_string($_POST['ticket_price']);
$pre_ticket_price = $mysqli->real_escape_string($_POST['pre_ticket_price']);
$max_num = $mysqli->real_escape_string($_POST['max_num']);
$start_num = $mysqli->real_escape_string($_POST['start_num']);

$list_id = 0;
$query = "SELECT * FROM ticket_list ORDER BY list_id ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $list_id = $row['list_id'];
}
$list_id = $list_id + 1;

$query = "INSERT INTO ticket_list (list_id, ticket_name, date, open_time, start_time, place, ticket_price, pre_ticket_price, max_num, start_num) VALUES ('$list_id', '$ticket_name', '$date', '$open_time', '$start_time', '$place', '$ticket_price', '$pre_ticket_price', '$max_num', '$start_num')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$list_id = sprintf('%05d', $list_id);
$list_id = strval($list_id);

$query = "CREATE TABLE ticket_$list_id (
        id int(6) UNSIGNED ZEROFILL PRIMARY KEY,
        issue_datetime datetime,
        token varchar(256),
        use_datetime datetime,
        issue_member_id int(5) UNSIGNED ZEROFILL,
        use_member_id int(5) UNSIGNED ZEROFILL
    );";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

header('Location: /member/mypage/login/');
exit();
