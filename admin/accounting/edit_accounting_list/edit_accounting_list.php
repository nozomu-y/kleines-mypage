<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$accounting_id = $_POST['fee_id'];
$accounting = new AccountingList($accounting_id);

if ($accounting->admin != 'GENERAL') {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$name = $mysqli->real_escape_string($_POST['name']);
$deadline = $mysqli->real_escape_string($_POST['deadline']);

$query = "UPDATE accounting_lists SET name='$name', deadline='$deadline' WHERE accounting_id='$accounting_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_update_fee'] = '';

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が新規集金リスト「" . $accounting->name . "」を編集しました。（名前：" . $accounting->name . "→" . $name . "，期限：" . $accounting->deadline . "→" . $deadline . "）\n", 3, __DIR__ . "/../../../Core/accounting.log");
header('Location: ' . MYPAGE_ROOT . '/admin/accounting/detail.php?fee_id=' . $accounting_id);
exit();
