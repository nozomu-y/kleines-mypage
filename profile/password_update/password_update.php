<?php
require __DIR__ . '/../../Common/init_page.php';

if (!isset($_POST['submit']) || !isset($_POST['old_password']) || !isset($_POST['new_password_1']) || !isset($_POST['new_password_2'])) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

$old_password = $_POST['old_password'];
$new_password_1 = $_POST['new_password_1'];
$new_password_2 = $_POST['new_password_2'];

if ($new_password_1 != $new_password_2) {
    $_SESSION['mypage_password_error'] = '';
    header('Location: ' . MYPAGE_ROOT . '/profile/password_update/');
    exit();
}

if (!preg_match('/^([\x21-\x7E]{8,})$/', $new_password_1)) {
    $_SESSION['mypage_password_regex_error'] = '';
    header('Location: ' . MYPAGE_ROOT . '/profile/password_update/');
    exit();
}

$query = "SELECT * FROM users WHERE user_id='$USER->id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $password_hash = $row['password'];
}

if (password_verify($old_password, $password_hash)) {
    $IP = $_SERVER["REMOTE_ADDR"];
    $query = "INSERT INTO password_updates (user_id, datetime, IP) VALUES ('$USER->id', now(), '$IP')";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $pass_hash = password_hash($new_password_1, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password='$pass_hash' WHERE user_id='$USER->id'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "がパスワードを更新しました。\n", 3, __DIR__ . "/../../Core/auth.log");
    $_SESSION['mypage_password_success'] = '';
    header('Location: ' . MYPAGE_ROOT);
    exit();
} else {
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "がパスワード更新時の認証に失敗しました。\n", 3, __DIR__ . "/../../Core/auth.log");
    $_SESSION['mypage_auth_error'] = '';
    header('Location: ' . MYPAGE_ROOT . '/profile/password_update/');
    exit();
}
