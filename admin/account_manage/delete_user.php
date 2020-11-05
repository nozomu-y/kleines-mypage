<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1)) {
    header('Location: ' . MYPAGE_ROOT);
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
    $query = "DROP TABLE individual_accounting_$id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DROP TABLE fee_record_$id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $query = "DROP TABLE bulletin_board_$id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    // make log file
    $_SESSION['mypage_delete_user'] = "";
    $_SESSION['mypage_account_name'] = $account->get_name();
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " deleted the account of " . $account->name . ". \n", 3, __DIR__ . "/../../Core/account_manage.log");
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/resign_list.php');
    exit();
}
