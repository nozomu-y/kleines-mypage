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

if (!isset($_GET['ticket_id'])) {
    header('Location: /member/mypage/login/admin/eticket/');
    exit();
} else {
    $ticket_id = $_GET['ticket_id'];
}



$query = "SELECT * FROM ticket_$list_id WHERE id = $ticket_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $ticket = new Ticket($row);
}

$data = $list_id . $ticket->id . $ticket->token;

require_once('/home/chorkleines/www/member/mypage/admin/eticket/vendor/autoload.php');

use Endroid\QrCode\QrCode;

$datetime = date("YmdHis");
$milisec = substr(explode(".", (microtime(true) . ""))[1], 0, 3);
$datetime .= $milisec;
$fpath = './qrcode/' . $datetime . '.png';


$qrCode = new QrCode($data);
header('Content-Type: ' . $qrCode->getContentType());
echo $qrCode->writeString();
