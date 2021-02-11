<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['delete'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$accounting_id = $_POST['delete'];
$accounting = new AccountingList($accounting_id);

if ($accounting->admin != 'GENERAL') {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

$query = "DELETE FROM accounting_records WHERE accounting_id=$accounting_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "UPDATE individual_accounting_records SET accounting_id=NULL WHERE accounting_id=$accounting_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "DELETE FROM accounting_lists WHERE accounting_id=$accounting_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が集金リスト「" . $accounting->name . "」を削除しました。\n", 3, __DIR__ . "/../../Core/accounting.log");
header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
exit();
