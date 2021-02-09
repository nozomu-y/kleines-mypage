<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant())) {
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
        if (!($part == 'S' || $part == 'A' || $part == 'T' || $part == 'B')) {
            print("Part name invalid");
            exit();
        }
        $query = "INSERT INTO users (email, status) VALUES ('$address', 'PRESENT')";
        $result = $mysqli->query($query);
        $new_id = $mysqli->insert_id;
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $query = "INSERT INTO profiles (user_id, last_name, first_name, name_kana, grade, part) VALUES ('$new_id', '$last_name','$first_name','$kana','$grade','$part')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が" . $last_name . $first_name . "のアカウントを追加しました。\n", 3, __DIR__ . "/../../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}
