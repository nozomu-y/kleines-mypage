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

if (!($user->admin == 1 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$fee_id = $_POST['fee_id'];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'check') !== false) {
        $account_id = explode('_', $key)[1];
        if ($value == 1) {
            $query = "SELECT * FROM fee_record_$account_id WHERE id = $fee_id";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $row_cnt = $result->num_rows;
            if ($row_cnt == 0) {
                $query = "INSERT INTO fee_record_$account_id (id, price) VALUES ('$fee_id', '$price')";
                $result = $mysqli->query($query);
                if (!$result) {
                    print('Query Failed : ' . $mysqli->error);
                    $mysqli->close();
                    exit();
                }
            }
        } else if ($value == 0) {
            $query = "DELETE FROM fee_record_$account_id WHERE id = $fee_id";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
        }
    }
}

$_SESSION['mypage_update_subject'] = '';

header('Location: /member/mypage/admin/accounting/detail.php?fee_id=' . $fee_id);
exit();
