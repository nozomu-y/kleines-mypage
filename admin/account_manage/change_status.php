<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 2 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
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
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " changed the status of " . $account->name . " to present. \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " changed the status of " . $account->name . " to absent. \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
        header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " changed the status of " . $account->name . " to resign. \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}
