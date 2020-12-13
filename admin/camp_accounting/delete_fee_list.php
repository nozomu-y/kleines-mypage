<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 5)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['delete'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
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

if ($fee_list->admin != 5) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
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
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . "が集金リスト「" . $fee_list->name . "」を削除しました。\n", 3, __DIR__ . "/../../Core/camp_accounting.log");
header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
exit();
