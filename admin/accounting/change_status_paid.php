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

if (!isset($_POST['fee_id'])) {
    header('Location: /member/mypage/admin/accounting/');
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
if ($fee_list->admin != 3) {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$user_id = $_POST['user_id'];
$price = $_POST['price'];
$paid_cash = $_POST['paid_cash'];

$query = "SELECT * FROM members WHERE id='$user_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$account = new User($result->fetch_assoc());

// amount of money paid from individual accounting
$paid_individual = '-' . strval(intval($price) - intval($paid_cash));
$query = "UPDATE fee_record_$account->id SET datetime = now(), paid_cash = $paid_cash WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

// if individual accounting was used
if (intval($price) - intval($paid_cash) > 0) {
    $query = "SELECT id FROM individual_accounting_$account->id ORDER BY id ASC";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $list_id = $row['id'];
    }
    $list_id = $list_id + 1;
    $query = "INSERT INTO individual_accounting_$account->id (id, date, name, price) VALUES ('$list_id', now(), '$fee_list->name', $paid_individual)";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
}


/* use google api to send an email */
require_once('/home/chorkleines/www/member/mypage/googleapi/mail.php');
$msg = new Google_Service_Gmail_Message();
$data = "";
$data .= "To: " . $account->email . "\n";
$data .= "Cc: \n";
$from = "コール・クライネス会計";
$data .= "From: " . mb_encode_mimeheader($from, 'utf-8') . " <kleines.webmaster@gmail.com>\n";
$subject = $fee_list->name . '完了のお知らせ';
$data .= "Subject: " . mb_encode_mimeheader($subject, 'utf-8') . "\n";
$data .= "\n";
$body = "コール・クライネス会計です。\n" . $fee_list->name . "（￥" . $price . "）の集金が完了致しました。\n";
if (intval($price) - intval($paid_cash) == 0) {
    $body .= "現金で￥" . $paid_cash . "を徴収しました。\n";
} else {
    $body .= "現金で￥" . $paid_cash . "を徴収し、￥" . strval(intval($price) - intval($paid_cash)) . "を個別会計の残高から差し引いています。\n";
}
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
if (intval($price) - intval($paid_cash) == 0) {
    error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "の「" . $fee_list->name . "」の提出状況を既納に変更し、現金で￥" . $paid_cash . "受け取りました。\n", 3, "/home/chorkleines/www/member/mypage/Core/accounting.log");
} else {
    error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が" . $account->name . "の「" . $fee_list->name . "」の提出状況を既納に変更し、現金で￥" . $paid_cash . "受け取り、個別会計から￥" . $paid_individual . "差し引きました。\n", 3, "/home/chorkleines/www/member/mypage/Core/accounting.log");
}
