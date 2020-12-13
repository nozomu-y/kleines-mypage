<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

if (isset($_POST['account_id'])) {
    $account_id = $_POST['account_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/individual_admin/accounting/');
    exit();
}

$query = "SELECT * FROM members WHERE id=$account_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$account = new User($result->fetch_assoc());

$date = $_POST['date'];
$name = $_POST['name'];
$price = $_POST['price'];

$query = "SELECT id FROM individual_accounting_$account->id ORDER BY id ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $list_id = $row['id'];
}
$list_id = $list_id + 1;

$query = "INSERT INTO individual_accounting_$account->id (id, date, name, price) VALUES ('$list_id', '$date', '$name', '$price')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}


$_SESSION['mypage_individual_add'] = $name;

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . "が" . $account->name . "の個別会計を追加しました。（項目名：" . $name . "　日付：" . $date . "　金額：" . $price . "）\n", 3, __DIR__ . "/../../Core/individual_accounting.log");
header('Location: detail.php?account_id=' . $account->id);
exit();
