<?php
session_start();

// logout the user who accessed logout.php
if (isset($_POST['logout'])) {
    $_SESSION = array();
    setcookie(session_name(), '', time() - 1, '/');
    session_destroy();

    require __DIR__.'/Common/dbconnect.php';
    if (!empty($_COOKIE['mypage_auto_login'])) {
        $token = $_COOKIE['mypage_auto_login'];
        // delete token from database
        $query = "DELETE FROM auto_login WHERE token = '$token'";
        $result = $mysqli->query($query);
        if (!$result) {
            print("Query Failed : " . $mysqli->error);
            $mysqli->close();
            exit();
        }
        // delete token from browser cookie
        setcookie("mypage_auto_login", "", time() - 60);
    }

    header("Location: ".MYPAGE_ROOT."/login/");
    exit();
} else {
    header("Location: ".MYPAGE_ROOT);
    exit();
}
