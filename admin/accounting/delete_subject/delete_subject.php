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
$accounting = new AccountingList($accounting_id);

$delete_count = 0;
foreach ($_POST as $key => $value) {
    if (strpos($key, 'check') !== false) {
        $user_id = explode('_', $key)[1];
        if ($value == 1) {
            $delete_count += 1;
            $query = "DELETE FROM accounting_records WHERE accounting_id=$accounting_id AND user_id=$user_id";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $account = new User($user_id);
            error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が集金リスト「" . $accounting->name . "」から" . $account->get_name() . "を削除しました。\n", 3, __DIR__ . "/../../../Core/accounting.log");
        }
    }
}

if ($delete_count >= 1) {
    $_SESSION['mypage_delete_subject'] = '';
}

header('Location: ' . MYPAGE_ROOT . '/admin/accounting/detail.php?fee_id=' . $accounting_id);
exit();
