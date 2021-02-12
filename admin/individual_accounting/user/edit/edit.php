<?php
require __DIR__ . '/../../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

if (isset($_POST['user_id']) && isset($_POST['list_id'])) {
    $user_id = $_POST['user_id'];
    $list_id = $_POST['list_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

$account = new User($user_id);
$price = $_POST['price'];

$query = "SELECT individual_accounting_lists.name, individual_accounting_records.list_id, individual_accounting_records.datetime, individual_accounting_records.price FROM individual_accounting_records INNER JOIN individual_accounting_lists ON individual_accounting_records.list_id=individual_accounting_lists.list_id WHERE individual_accounting_records.user_id=$user_id AND individual_accounting_records.list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $price_old = $row['price'];
    $name = $row['name'];
    $date = date('Y/m/d', strtotime($row['datetime']));
}

$query = "UPDATE individual_accounting_records SET price=$price WHERE list_id=$list_id AND user_id=$user_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_individual'] = $name;

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が" . $account->get_name() . "の個別会計を編集しました。（項目名：" . $name . "　日付：" . $date . "　金額：" . $price_old . "→" . $price . "）\n", 3, __DIR__ . "/../../../../Core/individual_accounting.log");
header('Location: ../?user_id=' . $account->id);
exit();
