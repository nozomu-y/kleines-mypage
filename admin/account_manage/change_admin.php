<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1)) {
    header('Location: ' . MYPAGE_ROOT);
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
        $_SESSION['mypage_account_name'] = $account->get_name();
        $_SESSION['mypage_admin'] = "マスター権限";
        // make log file
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " gave the administrator right (master) to " . $account->name . ". \n", 3, __DIR__ . "/../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
        $_SESSION['mypage_account_name'] = $account->get_name();
        $_SESSION['mypage_admin'] = "アカウント管理";
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " gave the administrator right (account management) to " . $account->name . ". \n", 3, __DIR__ . "/../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
        $_SESSION['mypage_account_name'] = $account->get_name();
        $_SESSION['mypage_admin'] = "会計システム";
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " gave the administrator right (accounting management) to " . $account->name . ". \n", 3, __DIR__ . "/../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
        $_SESSION['mypage_account_name'] = $account->get_name();
        $_SESSION['mypage_admin'] = "練習計画管理";
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " gave the administrator right (practice schedule management) to " . $account->name . ". \n", 3, __DIR__ . "/../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
        $_SESSION['mypage_account_name'] = $account->get_name();
        $_SESSION['mypage_admin'] = "合宿会計システム";
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " gave the administrator right (camp accounting management) to " . $account->name . ". \n", 3, __DIR__ . "/../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
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
        $_SESSION['mypage_account_name'] = $account->get_name();
        $_SESSION['mypage_admin_deprive'] = "";
        /** ログファイル作成の処理 **/
        error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . " deprived " . $account->name . " of the administrator right (" . $account->get_admin_en() . "). \n", 3, __DIR__ . "/../../Core/account_manage.log");
    }
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}
