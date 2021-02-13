<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_POST['present'])) {
    $user_id = $_POST['present'];
    $account = new User($user_id);
    $query = "UPDATE users SET status='PRESENT' WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $_SESSION['mypage_account_name'] = $account->get_name();
    $_SESSION['mypage_status'] = "在団";
    // make log file
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . " changed the status of " . $account->get_name() . " to present. \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
} else if (isset($_POST['absent'])) {
    $user_id = $_POST['absent'];
    $account = new User($user_id);
    $query = "UPDATE users SET status='ABSENT' WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $_SESSION['mypage_account_name'] = $account->get_name();
    $_SESSION['mypage_status'] = "休団";
    // make log file
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . " changed the status of " . $account->get_name() . " to absent. \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
} else if (isset($_POST['resign'])) {
    $user_id = $_POST['resign'];
    $account = new User($user_id);
    if ($account->isAdmin()) {
        $_SESSION['mypage_status_failure'] = "";
        $_SESSION['mypage_account_name'] = $account->get_name();
        header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
        exit();
    }
    $query = "UPDATE users SET status='RESIGNED' WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $_SESSION['mypage_account_name'] = $account->get_name();
    $_SESSION['mypage_status'] = "退団";
    // make log file
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . " changed the status of " . $account->get_name() . " to resign. \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}
