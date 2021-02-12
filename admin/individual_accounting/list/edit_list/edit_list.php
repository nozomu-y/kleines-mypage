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

if (isset($_POST['list_id'])) {
    $list_id = $_POST['list_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

$name = $_POST['name'];

$query = "SELECT * FROM individual_accounting_lists WHERE list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $name_old = $row['name'];
}

$query = "UPDATE individual_accounting_lists SET name='$name' WHERE list_id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_individual'] = $name;

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が個別会計を編集しました。（項目名：" . $name_old . "→" . $name . "）\n", 3, __DIR__ . "/../../../../Core/individual_accounting.log");
header('Location: ../');
exit();
