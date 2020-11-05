<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 2 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_POST['submit'])) {
    $array_csv = array();
    $lines = explode("\n", $_POST["csv"]);
    foreach ($lines as $line) {
        $array_csv[] = str_getcsv($line);
    }
    foreach ($array_csv as $line) {
        if ($line[0] == NULL) continue;
        $grade = trim($line[0]);
        $part = trim($line[1]);
        $last_name = trim($line[2]);
        $first_name = trim($line[3]);
        $kana = trim($line[4]);
        $address = $mysqli->real_escape_string(trim($line[5]));
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
                    price int(10),
                    fee_id int(3) UNSIGNED ZEROFILL
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
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . "が" . $last_name . $first_name . "のアカウントを追加しました。\n", 3, __DIR__ . "/../../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}
