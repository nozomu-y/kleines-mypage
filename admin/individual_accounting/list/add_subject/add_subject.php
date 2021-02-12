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

$list_id = $_POST['list_id'];
$price = $_POST['price'];
$individual_accounting = new IndividualAccountingList($list_id);

$query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, profiles.user_id, users.status, individual_accounting_records.price, individual_accounting_records.datetime, individual_accounting_records.list_id FROM profiles INNER JOIN users ON profiles.user_id=users.user_id LEFT OUTER JOIN (SELECT individual_accounting_records.price, individual_accounting_records.datetime, individual_accounting_records.list_id, individual_accounting_records.user_id FROM individual_accounting_records WHERE list_id=$list_id) as individual_accounting_records ON users.user_id=individual_accounting_records.user_id WHERE individual_accounting_records.list_id IS NULL AND users.status!='RESIGNED' ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
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
            $query = "INSERT INTO individual_accounting_records (list_id, price, user_id, datetime) VALUES ('$list_id', '$price','$user_id', now()) ON DUPLICATE KEY UPDATE price='$price'";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $account = new User($user_id);
            error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が個別会計「" . $individual_accounting->name . "」に" . $account->get_name() . "を追加しました。（金額：" . $price . "）\n", 3, __DIR__ . "/../../../../Core/individual_accounting.log");
        }
    }
}

if ($add_count >= 1) {
    $_SESSION['mypage_add_subject'] = '';
}

header('Location: ../detail.php?list_id=' . $list_id);
exit();
