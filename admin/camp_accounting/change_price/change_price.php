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

$accounting_id = $_POST['fee_id'];
$user_id = $_POST['account_id'];
$query = "SELECT *, (SELECT CONCAT(grade,part,' ',last_name,first_name) FROM profiles WHERE user_id=accounting_records.user_id) AS user_name FROM accounting_records INNER JOIN accounting_lists ON accounting_records.accounting_id=accounting_lists.accounting_id WHERE accounting_records.accounting_id=$accounting_id AND user_id=$user_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $accounting_price = $row['price'];
    $accounting_paid_cash = $row['paid_cash'];
    $accounting_datetime = $row['datetime'];
    $accounting_name = $row['name'];
    $accounting_deadline = $row['deadline'];
    $accounting_admin = $row['admin'];
    $name = $row['user_name'];
}
if ($accounting_admin != 'CAMP') {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

if ($accounting_datetime != NULL) {
    exit();
}

$price = $mysqli->real_escape_string($_POST['price']);

$query = "UPDATE accounting_records SET price='$price' WHERE user_id=$user_id AND accounting_id=$accounting_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_update_price'] = $price;
$_SESSION['mypage_account_name'] = $name;

// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が集金リスト「" . $accounting_name . "」の" . $name . "の集金金額を変更しました。（金額：" . $accounting_price . "→" . $price . "）\n", 3, __DIR__ . "/../../../Core/camp_accounting.log");
header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/detail.php?fee_id=' . $accounting_id);
exit();
