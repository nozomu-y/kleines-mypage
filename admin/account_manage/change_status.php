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

if (!($user->admin == 1 || $user->admin == 2 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

if (isset($_POST['present'])) {
    $id = $_POST['present'];
    $query = "SELECT * FROM members WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $account = new User($result->fetch_assoc());
    $query = "UPDATE members SET status = 0 WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $_SESSION['mypage_account_name'] = $account->get_name();
    $_SESSION['mypage_status'] = "在団";
    // make log file
    error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " changed the status of " . $account->name . " to present. \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    header('Location: /member/mypage/admin/account_manage/');
    exit();
} else if (isset($_POST['absent'])) {
    $id = $_POST['absent'];
    $query = "SELECT * FROM members WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $account = new User($result->fetch_assoc());
    $query = "UPDATE members SET status = 1 WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $_SESSION['mypage_account_name'] = $account->get_name();
    $_SESSION['mypage_status'] = "休団";
    // make log file
    error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " changed the status of " . $account->name . " to absent. \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    header('Location: /member/mypage/admin/account_manage/');
    exit();
} else if (isset($_POST['resign'])) {
    $id = $_POST['resign'];
    $query = "SELECT * FROM members WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $account = new User($result->fetch_assoc());
    if ($account->admin != NULL) {
        $_SESSION['mypage_status_failure'] = "";
        $_SESSION['mypage_account_name'] = $account->get_name();
        header('Location: /member/mypage/admin/account_manage/');
        exit();
    }
    $query = "UPDATE members SET status = 2 WHERE id = $id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $_SESSION['mypage_account_name'] = $account->get_name();
    $_SESSION['mypage_status'] = "退団";
    // make log file
    error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " changed the status of " . $account->name . " to resign. \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    header('Location: /member/mypage/admin/account_manage/');
    exit();
}
