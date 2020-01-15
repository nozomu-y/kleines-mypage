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

if (!($user->admin == 1 || $user->admin == 2)) {
    header('Location: /member/mypage/');
    exit();
}

if (isset($_POST['delete'])) {
    $id = $_POST['delete'];
    $query = "SELECT * FROM members WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $account = new User($result->fetch_assoc());
    $query = "DELETE FROM members WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $id = sprintf('%05d', $id);
    $id = strval($id);
    $query = "DROP TABLE individual_accounting_$id_u";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DROP TABLE fee_record_$id_u";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DROP TABLE bulletin_board_$id_u";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    // make log file
    error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "のアカウントを削除しました。\n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    header('Location: /member/mypage/admin/account_manage/');
    exit();
}
