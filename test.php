<?php
session_start();

// logout.phpにアクセスしたユーザーをログアウトする
if (isset($_GET['logout'])) {
    $_SESSION = array();
    setcookie(session_name(), '', time() - 1, '/');
    session_destroy();
}
