<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$accounting_id = $_POST['fee_id'];
$accounting_price = $_POST['price'];
$accounting = new AccountingList($accounting_id);

$query = "SELECT users.user_id, users.status FROM users LEFT OUTER JOIN (SELECT accounting_records.accounting_id, accounting_records.user_id FROM accounting_records WHERE accounting_id=$accounting_id) as accounting_records ON users.user_id=accounting_records.user_id WHERE accounting_records.accounting_id IS NULL AND users.status!='RESIGNED'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$unregistered = [];
while ($row = $result->fetch_assoc()) {
    array_push($unregistered, $row['user_id']);
}

$add_count = 0;
foreach ($_POST as $key => $value) {
    if (strpos($key, 'check') !== false) {
        $user_id = explode('_', $key)[1];
        if ($value == 1) {
            if (!in_array($user_id, $unregistered)) {
                continue;
            }
            $add_count += 1;
            $query = "INSERT INTO accounting_records (accounting_id, price, user_id) VALUES ('$accounting_id', '$accounting_price','$user_id') ON DUPLICATE KEY UPDATE price='$accounting_price'";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $account = new User($user_id);
            error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が集金リスト「" . $accounting->name . "」に" . $account->get_name() . "を追加しました。（金額：" . $accounting_price . "）\n", 3, __DIR__ . "/../../../Core/accounting.log");
        }
    }
}

if ($add_count >= 1) {
    $_SESSION['mypage_add_subject'] = '';
}

header('Location: ' . MYPAGE_ROOT . '/admin/accounting/detail.php?fee_id=' . $accounting_id);
exit();
