<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (!($user->admin == 1 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: /member/mypage/admin/individual_accounting/');
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
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "の個別会計を追加しました。（項目名：" . $name . "　日付：" . $date . "　金額：" . $price . "）\n", 3, "/home/chorkleines/www/member/mypage/Core/individual_accounting.log");
    }
}

$_SESSION['mypage_individual_add_multiple'] = $name;

header('Location: /member/mypage/admin/individual_accounting/');
exit();
