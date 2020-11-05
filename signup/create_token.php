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

if (isset($_SESSION['mypage_email']) && !$maintenance) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if ($maintenance) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (isset($_POST['signup'])) {
    $email = $mysqli->real_escape_string($_POST['email']);
    $query = "SELECT * FROM members WHERE email = '$email' AND status != 2";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result->num_rows;
    if ($row_cnt == 0) {
        $_SESSION['mypage_auth_error'] = "wrong-email";
        header('Location: ' . MYPAGE_ROOT . '/signup');
        exit();
    }
    $account = new User($row = $result->fetch_assoc());
    $token = md5(uniqid(rand(), true));
    $validation_url = 'https://' . WEB_DOMAIN . MYPAGE_ROOT . "/signup/auth.php?token=" . $token;
    $query = "UPDATE members SET token = '$token', validation_time = now() WHERE email = '$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    // send email useng GoogleApi
    require __DIR__ . '/../googleapi/mail.php';
    $msg = new Google_Service_Gmail_Message();
    $data = "";
    $data .= "To: " . $email . "\n";
    $data .= "Cc: \n";
    $from .= "コール・クライネスWeb管理人";
    $data .= "From: " . mb_encode_mimeheader($from, 'utf-8') . " <" . ADMIN_EMAIL . ">\n";
    $subject = "Kleines MyPage 本人確認のお知らせ";
    $data .= "Subject: " . mb_encode_mimeheader($subject, 'utf-8') . "\n";
    $data .= "MIME-Version: 1.0\n";
    $data .= "Content-Type: text/html; charset=utf-8\n";
    $data .= 'Content-Transfer-Encoding: quoted-printable' . "\n\n";
    $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>' . $subject . '</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
      body {
        width: 100%;
        height: 100%;
        margin: 0;
        font-size: 14px;
        font-family: Hiragino Sans, Hiragino Kaku Gothic ProN, Meiryo, Osaka, sans-serif;
        color: #686a78;
      }
      .bg-gray {
        width: 100%;
        height: 100%;
      }
      td {
        color: #686a78;
      }
      .header {
        width: 100%;
        text-align: center;
        padding-bottom: 24px;
      }
      h1 {
        margin: 0;
        color: #686a78;
        font-size: 24px;
      }
      h2 {
        font-size: 16px;
        color: #686a78;
      }
      p {
        color: #686a78;
      }
      .logo {
        width: 30%;
        padding-bottom: 14px;
        padding-top: 28px;
      }
      .main {
        width: 100%;
        background: #ffffff;
      }
      .footer {
        width: 100%;
      }
      table.content {
        width: 95%;
        max-width: 640px;
        margin: 0 auto;
        padding: 10px 0px;
      }
      .btn {
        margin: 0 1em;
        padding: 0.5em 2em;
        font-size: 16px;
        color: #ffffff;
        text-decoration: none;
        border-radius: 0.35rem;
        display: inline-block;
        background-color: #4e73df;
      }
      a {
        color: #4e73df;
      }
      .text-center {
        text-align: center;
      }
      .hr {
        width: 95%;
        max-width: 640px;
        margin: 0 auto;
        border-bottom: 2px #cfcfcf solid;
      }
    </style>
  </head>
  <body>
    <table class="header">
      <tr>
        <td class="bg-gray">
          <table class="content">
            <tr>
              <td class="text-center">
                <img class="logo" src="https://www.chorkleines.com/logo.png" />
              </td>
            </tr>
            <tr>
              <td><h1 class="text-center">Kleines Mypage</h1></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <div class="hr"></div>
    <table class="main">
      <tr>
        <td>
          <table class="content">
            <tr>
              <td>
                <h2>本人確認のお知らせ</h2>
                <p>' . $account->get_name() . 'さん</p>
                <p>アカウントを登録していただき、ありがとうございます。<br />以下のリンクから24時間以内にパスワードの設定を行ってください。</p>
                <p class="text-center"><a href="' . $validation_url . '">' . $validation_url . '</a></p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <div class="hr"></div>
    <table class="footer">
      <tr>
        <td class="bg-gray">
          <table class="content">
            <tr>
              <td>
                <p>
                  ■本メールに関するご意見・ご要望は、このメールにご返信ください。
                  <br />
                  ■Kleines Mypageへのアクセスは<a href="https://' . WEB_DOMAIN . MYPAGE_ROOT . '">こちら</a>から。
                </p>
                <p class="text-center">&copy; Chor Kleines 2020</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>';
    $data .= quoted_printable_encode($body);
    $data .= "\n";
    $data = base64_encode($data);
    $data = strtr($data, '+/', '-_');
    $data = rtrim($data, '=');
    $msg->setRaw($data);

    $service = new Google_Service_Gmail($client);
    $message = $msg;
    $message = $service->users_messages->send("me", $message);
    $_SESSION['mypage_auth_success'] = $email;
    header('Location: ' . MYPAGE_ROOT . '/signup');
    exit();
}
