<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['delete'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

$data = $_POST['delete'];
$data = explode('_', $data);
$user_id = $data[0];
$list_id = $data[1];

$account = new User($user_id);

$query = "SELECT individual_accounting_lists.name, individual_accounting_records.list_id, individual_accounting_records.datetime, individual_accounting_records.price FROM individual_accounting_records INNER JOIN individual_accounting_lists ON individual_accounting_records.list_id=individual_accounting_lists.list_id WHERE individual_accounting_records.user_id=$user_id AND individual_accounting_records.list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
}

$query = "DELETE FROM individual_accounting_records WHERE user_id=$user_id AND list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_individual_delete'] = $name;

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が" . $account->get_name() . "の個別会計データ「" . $name . "」を削除しました。\n", 3, __DIR__ . "/../../../Core/individual_accounting.log");
header('Location: ./?user_id=' . $account->id);
exit();
