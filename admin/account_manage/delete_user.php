<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isMaster())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_POST['delete'])) {
    $user_id = $_POST['delete'];
    $account = new User($user_id);
    $query = "DELETE FROM accounting_records WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DELETE FROM admins WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DELETE FROM identity_verifications WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DELETE FROM individual_accounting_records WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DELETE FROM login_histories WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DELETE FROM password_updates WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DELETE FROM profiles WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DELETE FROM users WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    // make log file
    $_SESSION['mypage_delete_user'] = "";
    $_SESSION['mypage_account_name'] = $account->get_name();
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . " deleted the account of " . $account->get_name() . ". \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/resign_list.php');
    exit();
}
