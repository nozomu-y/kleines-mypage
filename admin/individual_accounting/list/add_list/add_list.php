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

$name = $_POST['name'];

$query = "INSERT individual_accounting_lists (name, datetime) VALUES ('$name', now())";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_individual_add'] = $name;

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が個別会計を追加しました。（項目名：" . $name . "）\n", 3, __DIR__ . "/../../../../Core/individual_accounting.log");
header('Location: ../');
exit();
