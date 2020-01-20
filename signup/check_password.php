<?php
ob_start();
session_start();
if (isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/');
    exit();
}
require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');

if (isset($_POST['set_password'])) {
    $token = $_POST['token'];
    $query = "SELECT * FROM members WHERE token = '$token' AND status != 2";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $account = new User($result->fetch_assoc());

    $password1 = $mysqli->real_escape_string($_POST['password1']);
    $password2 = $mysqli->real_escape_string($_POST['password2']);
    if ($password1 != $password2) {
        $_SESSION['mypage_password_error'] = '';
        header('Location: /member/mypage/signup/auth.php?token=' . $token);
        exit();
    }
    $query = "UPDATE members SET login_failure = 0 WHERE email='$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $pass = password_hash($password1, PASSWORD_DEFAULT);
    $query = "UPDATE members SET password = '$pass', token = 'NULL', validation_time = 'NULL' WHERE token = '$token'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    error_log("[" . date('Y/m/d H:i:s') . "] " . $account->name . "がパスワードを設定しました。\n", 3, "/home/chorkleines/www/member/mypage/Core/auth.log");
    $_SESSION['mypage_password_success'] = '';
    header("Location: /member/mypage/login/");
    exit();
}
