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

if (!($user->admin == 1 || $user->admin == 2 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

if (isset($_POST['submit'])) {
    $array_csv = array();
    $lines = explode("\n", $_POST["csv"]);
    foreach ($lines as $line) {
        $array_csv[] = str_getcsv($line);
    }
    foreach ($array_csv as $line) {
        $last_name = $line[0];
        $first_name = $line[1];
        $kana = $line[2];
        $grade = $line[3];
        $address = $line[4];
        $part = $line[5];
        $query = "SELECT id FROM members ORDER BY id ASC";
        $result = $mysqli->query($query);
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $id = $id + 1;
        $query = "INSERT INTO members (id, email, last_name, first_name, kana, grade, part) VALUES ('$id', '$address', '$last_name', '$first_name', '$kana', '$grade', '$part')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $id = sprintf('%05d', $id);
        $id = strval($id);
        $query = "
                  CREATE TABLE individual_accounting_$id (
                    id int(3) UNSIGNED ZEROFILL PRIMARY KEY,
                    date date,
                    name varchar(256),
                    memo varchar(256),
                    price int(10)
                  );";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $query = "
                  CREATE TABLE fee_record_$id (
                    id int(3) UNSIGNED ZEROFILL PRIMARY KEY,
                    datetime datetime,
                    price int(10),
                    paid_cash int(10),
                    status int(1)
                  );";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $query = "
                  CREATE TABLE bulletin_board_$id (
                    id int(5) UNSIGNED ZEROFILL PRIMARY KEY,
                    datetime datetime
                  );";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $last_name . $first_name . "のアカウントを追加しました。\n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    }
    header('Location: /member/mypage/admin/account_manage/');
    exit();
}
