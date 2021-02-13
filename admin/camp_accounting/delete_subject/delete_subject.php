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
$accounting = new AccountingList($accounting_id);

$query = "SELECT users.user_id, users.status FROM users LEFT OUTER JOIN (SELECT accounting_records.datetime, accounting_records.accounting_id, accounting_records.user_id FROM accounting_records WHERE accounting_id=$accounting_id) as accounting_records ON users.user_id=accounting_records.user_id WHERE accounting_records.accounting_id IS NOT NULL AND accounting_records.datetime IS NULL";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$unpaid = [];
while ($row = $result->fetch_assoc()) {
    array_push($unpaid, $row['user_id']);
}

$delete_count = 0;
foreach ($_POST as $key => $value) {
    if (strpos($key, 'check') !== false) {
        $user_id = explode('_', $key)[1];
        if ($value == 1) {
            if (!in_array($user_id, $unpaid)) {
                continue;
            }
            $delete_count += 1;
            $query = "DELETE FROM accounting_records WHERE accounting_id=$accounting_id AND user_id=$user_id";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $account = new User($user_id);
            error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が集金リスト「" . $accounting->name . "」から" . $account->get_name() . "を削除しました。\n", 3, __DIR__ . "/../../../Core/camp_accounting.log");
        }
    }
}

if ($delete_count >= 1) {
    $_SESSION['mypage_delete_subject'] = '';
}

header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/detail.php?fee_id=' . $accounting_id);
exit();
