<?php
ob_start();
session_start();

require __DIR__.'/../Common/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ .'/../Common/function.php';
if (isset($_SESSION['mypage_email'])) {
    header('Location: '.MYPAGE_ROOT);
    exit();
}

if (isset($_POST['login'])) {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    // start query
    $query = "SELECT * FROM members WHERE email='$email' AND status != 2";
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
        error_log("[" . date('Y/m/d H:i:s') . "] " . "A non-existing email was entered. (email : " . $email . ", IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__."/../Core/auth.log");
        $_SESSION['mypage_auth_error'] = "wrong-email";
        header("Location: ".MYPAGE_ROOT."/login/");
        exit();
    } elseif ($row_cnt >= 2) {
        // if there is more than 2 accounts with the corresponding email
        // this code is not neccesary, just in case
        error_log("[" . date('Y/m/d H:i:s') . "] " . "The following email is registered to several accounts. (email : " . $email . ", IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__."/../Core/auth.log");
        $_SESSION['mypage_auth_error'] = "wrong-email";
        header("Location: ".MYPAGE_ROOT."/login/");
        exit();
    }
    $user = new User($result->fetch_assoc());
    $result->close();
    if ($user->login_failure >= 9) {
        // failed the authentication for more than 10 times
        $_SESSION['mypage_auth_error'] = "login-failure";
        header("Location: ".MYPAGE_ROOT."/login/");
        exit();
    } else {
        if (password_verify($password, $user->password)) {
            // passed the authentication
            $query = "UPDATE members SET login_failure = 0 WHERE email='$user->email'";
            $result = $mysqli->query($query);
            if (!$result) {
                print("Query Failed : " . $mysqli->error);
                $mysqli->close();
                exit();
            }
            // when "remember me" was selected at /login/index.php
            if (isset($_POST['remember_me']) && $_POST['remember_me'] == "checked") {
                // generate token
                $token = sha1(uniqid(rand(), true) . mt_rand(1, 999999999) . '_mypage_auto_login');
                // expiration time
                $expiration_time = 3600 * 24 * 30; // token valid for 30 days
                // set cookie
                setcookie("mypage_auto_login", $token, time() + $expiration_time, MYPAGE_ROOT, WEB_DOMAIN, false, true);
                // check device(platform) and browser
                require '../vendor/autoload.php';
                $ua_info = parse_user_agent();
                // check device
                $browser = $ua_info['browser'];
                $device = $ua_info['platform'];
                // add to database
                $query = "INSERT INTO auto_login (id, token, datetime, device, browser) VALUES ('$user->id', '$token', now(), '$device', '$browser')";
                $result = $mysqli->query($query);
                if (!$result) {
                    print("Query Failed : " . $mysqli->error);
                    $mysqli->close();
                    exit();
                }
            }
            $mysqli->close();
            // start session
            ob_start();
            session_start();
            $_SESSION['mypage_email'] = $user->email;
            // create log
            error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " logged in. \n", 3, __DIR__."/../Core/auth.log");
            header('Location: '.MYPAGE_ROOT);
            exit();
        } else {
            // authentication failure
            $query = "UPDATE members SET login_failure = login_failure + 1 WHERE email='$email'";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $mysqli->close();
            // create log
            error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " failed login authentication. " . "(IP Address : " . $_SERVER["REMOTE_ADDR"] . ")\n", 3, __DIR__."/../Core/auth.log");
            $_SESSION['mypage_auth_error'] = "wrong-password_" . strval($user->login_failure + 1);
            header("Location: ".MYPAGE_ROOT."/login/");
            exit();
        }
    }
}
