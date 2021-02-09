<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}

if (!isset($_POST['user_id']) || !isset($_POST['submit']) || !isset($_POST['last_name']) || !isset($_POST['first_name']) || !isset($_POST['name_kana'])  || !isset($_POST['email']) || !isset($_POST['grade']) || !isset($_POST['part'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}

$user_id = $_POST['user_id'];

$query = "SELECT profiles.last_name, profiles.first_name, profiles.name_kana, profiles.grade, profiles.part, users.email FROM profiles INNER JOIN users ON profiles.user_id=users.user_id WHERE profiles.user_id=$user_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $last_name_old = $row['last_name'];
    $first_name_old = $row['first_name'];
    $name_kana_old = $row['name_kana'];
    $grade_old = $row['grade'];
    $part_old = $row['part'];
    $email_old = $row['email'];
}

$last_name = $mysqli->real_escape_string($_POST['last_name']);
$first_name = $mysqli->real_escape_string($_POST['first_name']);
$name_kana = $mysqli->real_escape_string($_POST['name_kana']);
$email = $mysqli->real_escape_string($_POST['email']);
$grade = $mysqli->real_escape_string($_POST['grade']);
$part = $mysqli->real_escape_string($_POST['part']);
$query = "UPDATE profiles SET last_name='$last_name', first_name='$first_name', name_kana='$name_kana', part='$part', grade='$grade' WHERE user_id=$user_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "UPDATE users SET email='$email' WHERE user_id=$user_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$admin = '';
$admin_old = '';
if ($USER->isMaster()) {
    $query = "SELECT * FROM admins WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $admin_old .= ',';
        $admin_old .= $row['role'];
    }

    $query = "DELETE FROM admins WHERE user_id=$user_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }

    if (isset($_POST['master'])) {
        $admin .= ',MASTER';
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'MASTER')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
    }
    if (isset($_POST['manager'])) {
        $admin .= ',MANAGER';
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'MANAGER')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
    }
    if (isset($_POST['accountant'])) {
        $admin .= ',ACCOUNTANT';
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'ACCOUNTANT')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
    }
    if (isset($_POST['camp'])) {
        $admin .= ',CAMP';
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'CAMP')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
    }
}

error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "がユーザーの情報を編集しました。（編集前：" . $grade_old . "," . $part_old . "," . $last_name_old . "," . $first_name_old . "," . $name_kana_old . "," . $email_old . $admin_old . "）（編集後：" . $grade . "," . $part . "," . $last_name . "," . $first_name . "," . $name_kana . "," . $email . $admin . "）\n", 3, __DIR__ . "/../../Core/account_manage.log");

header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
exit();
