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

$list_id = $_POST['delete'];

$query = "SELECT * FROM individual_accounting_lists WHERE list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
}

$query = "DELETE FROM individual_accounting_lists WHERE list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_individual_delete'] = $name;

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が個別会計「" . $name . "」を削除しました。\n", 3, __DIR__ . "/../../../Core/individual_accounting.log");
header('Location: ./');
exit();
