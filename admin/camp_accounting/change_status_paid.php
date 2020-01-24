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

if (!($user->admin == 1 || $user->admin == 5)) {
    header('Location: /member/mypage/');
    exit();
}

if (!isset($_POST['fee_id'])) {
    header('Location: /member/mypage/admin/camp_accounting/');
    exit();
}

$fee_id = $_POST['fee_id'];
$query = "SELECT * FROM fee_list WHERE id=$fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$fee_list = new Fee_List($result->fetch_assoc());
if ($fee_list->admin != 5) {
    header('Location: /member/mypage/admin/camp_accounting/');
    exit();
}

$user_id = $_POST['user_id'];
$price = $_POST['price'];

$query = "SELECT * FROM members WHERE id='$user_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$account = new User($result->fetch_assoc());

$query = "UPDATE fee_record_$account->id SET datetime = now(), paid_cash = $price WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}


/* use google api to send an email */
require_once('/home/chorkleines/www/member/mypage/googleapi/mail.php');
$msg = new Google_Service_Gmail_Message();
$data = "";
$data .= "To: " . $account->email . "\n";
$data .= "Cc: \n";
$from = "コール・クライネス合宿委員";
$data .= "From: " . mb_encode_mimeheader($from, 'utf-8') . " <kleines.webmaster@gmail.com>\n";
$subject = $fee_list->name . '完了のお知らせ';
$data .= "Subject: " . mb_encode_mimeheader($subject, 'utf-8') . "\n";
$data .= "\n";
$body = "コール・クライネス会計です。\n" . $fee_list->name . "（￥" . $price . "）の集金が完了致しました。\n";
$body .= "現金で￥" . $price . "を徴収しました。\n";
$body .= "お支払いただきありがとうございます。\n";
$body .= "\n支払った覚えのない方は会計またはWeb管までご連絡ください。";
$body .= "\n\nマイページへのアクセスは https://www.chorkleines.com/member/mypage/ から。";
$data .= $body;
$data = base64_encode($data);
$data = strtr($data, '+/', '-_');
$data = rtrim($data, '=');
$msg->setRaw($data);
// make object
$service = new Google_Service_Gmail($client);
// make message
$message = $msg;
//send message
$message = $service->users_messages->send("me", $message);
// get absolute number
$paid_individual *= -1;
// make log file
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "の「" . $fee_list->name . "」の提出状況を既納に変更し、現金で￥" . $price . "受け取りました。\n", 3, "/home/chorkleines/www/member/mypage/Core/camp_accounting.log");

$_SESSION['mypage_account_name'] = $account->get_name();
$_SESSION['mypage_fee_status'] = "既納";

header('Location: /member/mypage/admin/camp_accounting/detail.php?fee_id=' . $fee_list->id);
exit();
