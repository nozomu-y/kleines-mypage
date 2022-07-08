<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['fee_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$accounting_id = $_POST['fee_id'];
$accounting = new AccountingList($accounting_id);

if ($accounting->admin != 'GENERAL') {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$prices = array();
foreach ($_POST as $key => $value) {
    if (strpos($key, 'price') === false) continue;
    $user_id = explode('_', $key)[1];
    $prices[$user_id] = -1 * $value;
}
$bcc = "";
foreach ($_POST as $key => $value) {
    if (strpos($key, 'email') === false) continue;
    $user_id = explode('_', $key)[1];
    $bcc .= $value . ",";
}
$names = "";
foreach ($_POST as $key => $value) {
    if (strpos($key, 'name') === false) continue;
    $user_id = explode('_', $key)[1];
    $names .= $value . ", ";
}
$names = substr($names, 0, -2);

$account = new User($user_id);

// execute query
$query = "";
foreach ($prices as $user_id => $price) {
    $query .= "UPDATE accounting_records SET datetime = now(), paid_cash=0 WHERE user_id=$user_id AND accounting_id=$accounting_id;";
    $query .= "INSERT INTO individual_accounting_records (user_id, datetime, price, accounting_id) VALUES ('$user_id', now(), '$price','$accounting_id');";
}
$result = $mysqli->multi_query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

/* use google api to send an email */
require __DIR__ . '/../../../googleapi/mail.php';
$msg = new Google_Service_Gmail_Message();
$data = "";
$data .= "To: \n";
$data .= "Cc: \n";
$data .= "Bcc: " . $bcc . "\n";
$from = "コール・クライネス会計";
$data .= "From: " . mb_encode_mimeheader($from, 'utf-8') . " <kleines.webmaster@gmail.com>\n";
$subject = '【' . $accounting->name . '】集金完了のお知らせ';
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
                <h2>' . $subject . '</h2>
                <p>コール・クライネス会計です。<br />' . $accounting->name . 'の集金が完了致しました。<br/>お支払いただきありがとうございます。<br>
                    詳細は<a href="https://' . WEB_DOMAIN . MYPAGE_ROOT . '">Kleines Mypage</a>からご確認ください。</p>
                <p>支払った覚えのない方は会計またはWeb管までご連絡ください。</p>
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
                <p class="text-center">&copy; Chor Kleines ' . date("Y") . '</p>
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
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が" . $names . "の「" . $accounting->name . "」の提出状況を既納に変更しました。\n", 3, __DIR__ . "/../../../Core/accounting.log");

$_SESSION['mypage_account_name'] = $names;
$_SESSION['mypage_fee_status'] = "既納";

header('Location: ' . MYPAGE_ROOT . '/admin/accounting/detail.php?fee_id=' . $accounting->accounting_id);
exit();
