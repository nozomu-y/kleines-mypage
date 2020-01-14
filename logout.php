<?php
session_start();

// logout.phpにアクセスしたユーザーをログアウトする
if (isset($_POST['logout'])) {
    $_SESSION = array();
    setcookie(session_name(), '', time() - 1, '/');
    session_destroy();
    header("Location: /member/mypage/login/");
    exit();
} else {
    header("Location: /member/mypage/");
    exit();
}
