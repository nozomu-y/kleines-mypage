<?php
ob_start();
session_start();

require __DIR__ . '/../Common/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ . '/../Common/function.php';
if (isset($_SESSION['mypage_user_id'])) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_POST['login'])) {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    // start query
    $query = "SELECT * FROM users WHERE email='$email'";
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
        header("Location: " . MYPAGE_ROOT . "/login/");
        exit();
    } elseif ($row_cnt >= 2) {
        // if there is more than 2 accounts with the corresponding email
        // this code is not neccesary, just in case
        error_log("[" . date('Y/m/d H:i:s') . "] " . "The following email is registered to several accounts. (email : " . $email . ", IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__ . "/../Core/auth.log");
        $_SESSION['mypage_auth_error'] = "wrong-email";
        header("Location: " . MYPAGE_ROOT . "/login/");
        exit();
    }
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];
    $password_hash = $user['password'];
    $status = $user['status'];

    if (!strcmp($status, "RESIGNED")) {
        error_log("[" . date('Y/m/d H:i:s') . "] " . "An email of resigned user was entered. (email : " . $email . ", IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__ . "/../Core/auth.log");
        $_SESSION['mypage_auth_error'] = "resigned";
        header("Location: " . MYPAGE_ROOT . "/login/");
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
        header("Location: " . MYPAGE_ROOT . "/login/");
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
            // when "remember me" was selected at /login/index.php
            // if (isset($_POST['remember_me']) && $_POST['remember_me'] == "checked") {
            //     // generate token
            //     $token = sha1(uniqid(rand(), true) . mt_rand(1, 999999999) . '_mypage_auto_login');
            //     // expiration time
            //     $expiration_time = 3600 * 24 * 30; // token valid for 30 days
            //     // set cookie
            //     setcookie("mypage_auto_login", $token, time() + $expiration_time, MYPAGE_ROOT, WEB_DOMAIN, false, true);
            //     // check device(platform) and browser
            //     require '../vendor/autoload.php';
            //     $ua_info = parse_user_agent();
            //     // check device
            //     $browser = $ua_info['browser'];
            //     $device = $ua_info['platform'];
            //     // add to database
            //     $query = "INSERT INTO auto_login (id, token, datetime, device, browser) VALUES ('$user->id', '$token', now(), '$device', '$browser')";
            //     $result = $mysqli->query($query);
            //     if (!$result) {
            //         print("Query Failed : " . $mysqli->error);
            //         $mysqli->close();
            //         exit();
            //     }
            // }
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
            header("Location: " . MYPAGE_ROOT . "/login/");
            exit();
        }
    }
}
