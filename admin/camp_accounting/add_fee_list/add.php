<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 5)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

$name = $mysqli->real_escape_string($_POST['name']);
$deadline = $mysqli->real_escape_string($_POST['deadline']);
$price = $mysqli->real_escape_string($_POST['price']);

$query = "SELECT id FROM fee_list ORDER BY id ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $fee_id = $row['id'];
}
$fee_admin = 5;
$fee_id = $fee_id + 1;
$query = "INSERT INTO fee_list (id, name, deadline, price, admin) VALUES ('$fee_id', '$name', '$deadline', '$price', '$fee_admin')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が新規集金リスト「" . $name . "」を追加しました。（期限：" . $deadline . "、金額：" . $price . "）\n", 3, __DIR__ . "/../../../Core/camp_accounting.log");
header('Location: subject.php?fee_id=' . $fee_id);
exit();
