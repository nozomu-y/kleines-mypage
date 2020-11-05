<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$fee_id = $_POST['fee_id'];
$query = "SELECT * FROM fee_list WHERE id='$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee_list = new Fee_List($result->fetch_assoc());
if ($fee_list->admin != 3) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$name = $mysqli->real_escape_string($_POST['name']);
$deadline = $mysqli->real_escape_string($_POST['deadline']);

$query = "UPDATE fee_list SET name = '$name', deadline = '$deadline' WHERE id = '$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_update_fee'] = '';

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . "が新規集金リスト「" . $name . "」を編集しました。（期限：" . $deadline . "）\n", 3, __DIR__ . "/../../../Core/accounting.log");
header('Location: ' . MYPAGE_ROOT . '/admin/accounting/detail.php?fee_id=' . $fee_id);
exit();
