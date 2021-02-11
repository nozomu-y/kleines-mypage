<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isCamp())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

$name = $mysqli->real_escape_string($_POST['name']);
$deadline = $mysqli->real_escape_string($_POST['deadline']);

$query = "SELECT accounting_id FROM accounting_lists ORDER BY accounting_id ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $accounting_id = $row['accounting_id'];
}
$accounting_id = $accounting_id + 1;
$query = "INSERT INTO accounting_lists (accounting_id, name, deadline, admin) VALUES ('$accounting_id', '$name', '$deadline', 'CAMP')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が新規集金リスト「" . $name . "」を追加しました。（期限：" . $deadline . "）\n", 3, __DIR__ . "/../../../Core/camp_accounting.log");
header('Location: ../detail.php?fee_id=' . $accounting_id);
exit();
