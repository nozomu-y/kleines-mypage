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
    header('Location: /member/mypage/login/admin/eticket/');
    exit();
} else {
    $list_id = $_GET['list_id'];
}

$query = "SELECT * FROM ticket_list WHERE list_id = $list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $ticket_list = new Ticket_List($row);
}

$ticket_id = 0;
$query = "SELECT * FROM ticket_$ticket_list->id ORDER BY id ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $ticket = new Ticket($row);
}
$ticket_id = $ticket->id + 1;
if ($ticket_list->start_num > $ticket_id) {
    $ticket_id = $ticket_list->start_num;
}
if ($ticket_id >= $ticket_list->max_num + $ticket_list->start_num) {
    exit();
}

$token = md5(uniqid(rand(), true));
$query = "INSERT INTO ticket_$ticket_list->id (id, issue_datetime, token) VALUES ('$ticket_id', NOW(), '$token')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

header('Location: /member/mypage/admin/eticket/tickets/?list_id=' . $ticket_list->id);
exit();
