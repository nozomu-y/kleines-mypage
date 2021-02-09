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

if (!$maintenance) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (isset($_POST['login'])) {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    // start query
    $query = "SELECT * FROM users WHERE email='$email' AND status != 'RESIGNED'";
    $result = $mysqli->query($query);
    // error check
    if (!$result) {
        print("Query Failed : " . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result->num_rows;
    if ($row_cnt == 0) {
        // there is no such account with the corresponding email
        // create log
        error_log("[" . date('Y/m/d H:i:s') . "] " . "A non-existing email was entered. (email : " . $email . ", IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__ . "/../Core/auth.log");
        $_SESSION['mypage_auth_error'] = "wrong-email";
        header("Location: " . MYPAGE_ROOT . "/login/admin_login.php");
        exit();
    } else if ($row_cnt >= 2) {
        // if there is more than 2 accounts with the corresponding email
        // this code is not neccesary, just in case
        error_log("[" . date('Y/m/d H:i:s') . "] " . "The following email is registered to several accounts. (email : " . $email . ", IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__ . "/../Core/auth.log");
        $_SESSION['mypage_auth_error'] = "wrong-email";
        header("Location: " . MYPAGE_ROOT . "/login/admin_login.php");
        exit();
    }
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];
    $password_hash = $user['password'];
    $status = $user['status'];

    $query = "SELECT * FROM admins WHERE user_id='$user_id' AND role='MASTER'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result->num_rows;
    if ($row_cnt != 1) {
        $_SESSION['mypage_auth_error'] = "unauthorized";
        header("Location: " . MYPAGE_ROOT . "/login/admin_login.php");
        exit();
    }

    $last_update = NULL;
    $query = "SELECT datetime from password_updates WHERE user_id='$user_id' ORDER BY datetime DESC LIMIT 1";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $last_update = $row['datetime'];
    }

    $query = "SELECT datetime, success from login_histories WHERE user_id='$user_id' ORDER BY datetime DESC LIMIT 10";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $login_failure = 0;
    while ($row = $result->fetch_assoc()) {
        if (strtotime($last_update) < strtotime($row['datetime'])) {
            if ($row['success'] == 0) {
                $login_failure++;
            } else {
                break;
            }
        }
    }

    if ($login_failure >= 9) {
        // failed the authentication for more than 10 times
        $_SESSION['mypage_auth_error'] = "login-failure";
        header("Location: " . MYPAGE_ROOT . "/login/admin_login.php");
        exit();
    } else {
        if (password_verify($password, $password_hash)) {
            // passed the authentication
            $IP = $_SERVER["REMOTE_ADDR"];
            $query = "INSERT INTO login_histories (user_id, datetime, success, IP) VALUES ('$user_id', now(), 1, '$IP')";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $mysqli->close();
            // start session
            ob_start();
            session_start();
            $_SESSION['mypage_user_id'] = $user_id;
            // create log
            error_log("[" . date('Y/m/d H:i:s') . "] " . $email . " logged in. \n", 3, __DIR__ . "/../Core/auth.log");
            header('Location: ' . MYPAGE_ROOT);
            exit();
        } else {
            // authentication failure
            $IP = $_SERVER["REMOTE_ADDR"];
            $query = "INSERT INTO login_histories (user_id, datetime, success, IP) VALUES ('$user_id', now(), 0, '$IP')";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $mysqli->close();
            // create log
            error_log("[" . date('Y/m/d H:i:s') . "] " . $email . " failed login authentication. " . "(IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__ . "/../Core/auth.log");
            $_SESSION['mypage_auth_error'] = "wrong-password_" . strval($login_failure + 1);
            header("Location: " . MYPAGE_ROOT . "/login/admin_login.php");
            exit();
        }
    }
}
