<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . 'admin/individual_accounting/');
    exit();
}

$name = $_POST['name'];
$date = $_POST['date'];
$price = $_POST['price'];

foreach ($_POST as $key => $value) {
    if (strpos($key, 'check') !== false) {
        $account_id = explode('_', $key)[1];
        $query = "SELECT * FROM members WHERE id = $account_id";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $account = new User($result->fetch_assoc());
        $query = "SELECT id FROM individual_accounting_$account_id ORDER BY id ASC";
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
        $query = "INSERT INTO individual_accounting_$account_id (id, date, name, price) VALUES ('$list_id', '$date', '$name', '$price')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が" . $account->get_name() . "の個別会計を追加しました。（項目名：" . $name . "　日付：" . $date . "　金額：" . $price . "）\n", 3, __DIR__ . "/../../../Core/individual_accounting.log");
    }
}

$_SESSION['mypage_individual_add_multiple'] = $name;

header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
exit();
