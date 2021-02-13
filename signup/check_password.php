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

if (isset($_SESSION['mypage_user_id']) && !$maintenance) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if ($maintenance) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (isset($_POST['set_password'])) {
    $token = $_POST['token'];
    $query = "SELECT * FROM identity_verifications WHERE token = '$token'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    $USER = new User($user_id);

    $password1 = $mysqli->real_escape_string($_POST['password1']);
    $password2 = $mysqli->real_escape_string($_POST['password2']);
    if ($password1 != $password2) {
        $_SESSION['mypage_password_error'] = '';
        header('Location: ' . MYPAGE_ROOT . '/signup/auth.php?token=' . $token);
        exit();
    }
    $IP = $_SERVER["REMOTE_ADDR"];
    $query = "INSERT INTO password_updates (user_id, datetime, IP) VALUES ('$user_id', now(), '$IP')";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $pass_hash = password_hash($password1, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password = '$pass_hash' WHERE user_id='$user_id'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "がパスワードを設定しました。\n", 3, __DIR__ . "/../Core/auth.log");
    $_SESSION['mypage_password_success'] = '';
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}
