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

if (!($user->admin == 1 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

if (!isset($_POST['delete'])) {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$fee_id = $_POST['delete'];
$query = "SELECT * FROM fee_list WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee_list = new Fee_List($result->fetch_assoc());

if ($fee_list->admin != 3) {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$query = "SELECT * FROM members ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $account = new User($row);
    $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_list->id";
    $result_1 = $mysqli->query($query);
    if (!$result_1) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result_1->num_rows;
    if ($row_cnt != 0) {
        $fee = new Fee($result_1->fetch_assoc());
        if ($fee->paid_individual != 0) {
            $query = "DELETE FROM individual_accounting_$account->id WHERE name = '$fee_list->name'";
            echo ($query);
            $result_1 = $mysqli->query($query);
            if (!$result_1) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
        }
        $query = "DELETE FROM fee_record_$account->id WHERE id = $fee_id";
        echo ($query);
        $result_1 = $mysqli->query($query);
        if (!$result_1) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
    }
}

$query = "DELETE FROM fee_list WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が集金リスト「" . $fee_list->name . "」を削除しました。\n", 3, "/home/chorkleines/www/member/mypage/Core/accounting.log");
header('Location: /member/mypage/admin/accounting/');
exit();
