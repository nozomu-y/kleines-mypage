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
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (isset($_POST['delete'])) {
    $token = $_POST['delete'];
} else {
    header('Location: /member/mypage/profile/sessions.php');
    exit();
}

$query = "SELECT * FROM auto_login WHERE token = '$token'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
}
if ($user_id == $user->id) {
    $query = "DELETE FROM auto_login WHERE token = '$token'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
}

header('Location: /member/mypage/profile/sessions.php');
exit();
