<?php
session_start();

// logout.phpにアクセスしたユーザーをログアウトする
if (isset($_POST['logout'])) {
    $_SESSION = array();
    setcookie(session_name(), '', time() - 1, '/');
    session_destroy();

    require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
    if (!empty($_COOKIE['mypage_auto_login'])) {
        $token = $_COOKIE['mypage_auto_login'];
        // delete token from database
        $query = "DELETE FROM auto_login WHERE token = $token";
        $result = $mysqli->query($query);
        if (!$result) {
            print("Query Failed : " . $mysqli->error);
            $mysqli->close();
            exit();
        }
        // delete token from browser cookie
        setcookie("mypage_auto_login", "", time() - 60);
    }

    header("Location: /member/mypage/login/");
    exit();
} else {
    header("Location: /member/mypage/");
    exit();
}
