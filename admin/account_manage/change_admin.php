<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (!($user->admin == 1)) {
    header('Location: /member/mypage/');
    exit();
}

if (isset($_POST['admin-give-1'])) {
    $check_admin = $_POST['check'];
    foreach ($check_admin as $value) {
        $query = "SELECT * FROM members WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $account = new User($result->fetch_assoc());
        $query = "UPDATE members SET admin = 1 WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        // make log file
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " gave the administrator right (master) to " . $account->name . ". \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    }
    header('Location: /member/mypage/admin/account_manage/');
    exit();
} elseif (isset($_POST['admin-give-2'])) {
    $check_admin = $_POST['check'];
    foreach ($check_admin as $value) {
        $query = "SELECT * FROM members WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $account = new User($result->fetch_assoc());
        $query = "UPDATE members SET admin = 2 WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " gave the administrator right (account management) to " . $account->name . ". \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    }
    header('Location: /member/mypage/admin/account_manage/');
    exit();
} elseif (isset($_POST['admin-give-3'])) {
    $check_admin = $_POST['check'];
    foreach ($check_admin as $value) {
        $query = "SELECT * FROM members WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $account = new User($result->fetch_assoc());
        $query = "UPDATE members SET admin = 3 WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " gave the administrator right (accounting management) to " . $account->name . ". \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    }
    header('Location: /member/mypage/admin/account_manage/');
    exit();
} elseif (isset($_POST['admin-give-4'])) {
    $check_admin = $_POST['check'];
    foreach ($check_admin as $value) {
        $query = "SELECT * FROM members WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $account = new User($result->fetch_assoc());
        $query = "UPDATE members SET admin = 4 WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " gave the administrator right (practice schedule management) to " . $account->name . ". \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    }
    header('Location: /member/mypage/admin/account_manage/');
    exit();
} elseif (isset($_POST['admin-give-5'])) {
    $check_admin = $_POST['check'];
    foreach ($check_admin as $value) {
        $query = "SELECT * FROM members WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $account = new User($result->fetch_assoc());
        $query = "UPDATE members SET admin = 5 WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " gave the administrator right (camp accounting management) to " . $account->name . ". \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    }
    header('Location: /member/mypage/admin/account_manage/');
    exit();
} elseif (isset($_POST['admin-take'])) {
    $check_admin = $_POST['check'];
    foreach ($check_admin as $value) {
        $query = "SELECT * FROM members WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $account = new User($result->fetch_assoc());
        $query = "UPDATE members SET admin = NULL WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        /** ログファイル作成の処理 **/
        error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " deprived " . $account->name . " of the administrator right (" . $account->get_admin_en() . "). \n", 3, "/home/chorkleines/www/member/mypage/Core/account_manage.log");
    }
    header('Location: /member/mypage/admin/account_manage/');
    exit();
}
