<?php
ob_start();
session_start();
if (isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/');
    exit();
}
require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');

if (isset($_POST['signup'])) {
    $email = $mysqli->real_escape_string($_POST['email']);
    $query = "SELECT * FROM members WHERE email = '$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $account = new User($row = $result->fetch_assoc());
    $row_cnt = $result->num_rows;
    if ($row_cnt == 0) {
        $_SESSION['mypage_auth_error'] = "wrong-email";
        header('Location: /member/mypage/signup/');
        exit();
    }
    $token = md5(uniqid(rand(), true));
    $validation_url = "https://www.chorkleines.com/member/mypage/signup/auth.php?token=" . $token;
    $query = "UPDATE members SET token = '$token', validation_time = now() WHERE email = '$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    /* GoogleApi を用いてメールを送信する */
    require '/home/chorkleines/www/member/mypage/googleapi/mail.php';
    $msg = new Google_Service_Gmail_Message();
    $data = "";
    $data .= "To: " . $email . "\n"; //送信先
    $data .= "Cc: \n"; //CC
    $from .= "コール・クライネスWeb管理人";
    $data .= "From: " . mb_encode_mimeheader($from, 'utf-8') . " <kleines.webmaster@gmail.com>\n";
    $subject = "Kleines MyPage 本人確認のお知らせ"; //タイトル
    $data .= "Subject: " . mb_encode_mimeheader($subject, 'utf-8') . "\n";
    $data .= "\n"; //改行２回でヘッダー部分を区別
    $body = $account->name . "さん\n\nアカウントを登録していただき、ありがとうございます。\n以下のリンクから24時間以内にパスワードの設定を行ってください。\n\n" . $validation_url . "\n\nコール・クライネス Web管理人";
    $data .= $body;
    $data = base64_encode($data); //base64エンコードする
    $data = strtr($data, '+/', '-_'); //サニタイジング
    $data = rtrim($data, '='); //最後の'='を除去
    $msg->setRaw($data); //データをセット
    //オブジェクト生成
    $service = new Google_Service_Gmail($client);
    //メッセージ作成
    $message = $msg;
    //メッセージ送信
    $message = $service->users_messages->send("me", $message);
    $_SESSION['mypage_auth_success'] = $email;
    header('Location: /member/mypage/signup/');
    exit();
}
