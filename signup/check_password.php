<?php
ob_start();
session_start();

require __DIR__ . '/../Common/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ . '/../Common/function.php';

if (strcmp(getGitBranch(), "master") && WEB_DOMAIN == "chorkleines.com") {  // if current branch is not master
    $maintenance = true;
} else {
    $maintenance = false;
}

if (isset($_SESSION['mypage_email']) && !$maintenance) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if ($maintenance) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (isset($_POST['set_password'])) {
    $token = $_POST['token'];
    $query = "SELECT * FROM members WHERE token = '$token' AND status != 2";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $USER = new User($result->fetch_assoc());

    $password1 = $mysqli->real_escape_string($_POST['password1']);
    $password2 = $mysqli->real_escape_string($_POST['password2']);
    if ($password1 != $password2) {
        $_SESSION['mypage_password_error'] = '';
        header('Location: ' . MYPAGE_ROOT . '/signup/auth.php?token=' . $token);
        exit();
    }
    $query = "UPDATE members SET login_failure = 0 WHERE email='$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $pass_hash = password_hash($password1, PASSWORD_DEFAULT);
    $query = "UPDATE members SET password = '$pass_hash', token = NULL, validation_time = NULL WHERE token = '$token'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . "がパスワードを設定しました。\n", 3, __DIR__ . "/../Core/auth.log");
    $_SESSION['mypage_password_success'] = '';
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}
