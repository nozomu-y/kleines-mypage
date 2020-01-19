<?php
  require '/home/chorkleines/www/member/download/18/mypage/googleapi/vendor/autoload.php';
  date_default_timezone_set('Asia/Tokyo');
  function getClient()
  {
      $client = new Google_Client();
      $client->setApplicationName('Gmail API PHP Quickstart');
      $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
      $client->setScopes('https://www.googleapis.com/auth/gmail.send');
      $client->setAuthConfig('/home/chorkleines/www/member/download/18/mypage/googleapi/credentials.json');
      $client->setApprovalPrompt('auto');
      $client->setAccessType('offline');
      $client->setPrompt('select_account consent');

      $tokenPath = '/home/chorkleines/www/member/download/18/mypage/googleapi/token.json';
      if (file_exists($tokenPath)) {
          $accessToken = json_decode(file_get_contents($tokenPath), true);
          $client->setAccessToken($accessToken);
      }

      // If there is no previous token or it's expired.
      if ($client->isAccessTokenExpired()) {
          // Refresh the token if possible, else fetch a new one.
          if ($client->getRefreshToken()) {
              $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
          } else {
              // Request authorization from the user.
              $authUrl = $client->createAuthUrl();
              printf("Open the following link in your browser:\n%s\n", $authUrl);
              print 'Enter verification code: ';
              $authCode = trim(fgets(STDIN));

              // Exchange authorization code for an access token.
              $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
              $client->setAccessToken($accessToken);

              // Check to see if there was an error.
              if (array_key_exists('error', $accessToken)) {
                  throw new Exception(join(', ', $accessToken));
              }
          }
          // Save the token to a file.
          if (!file_exists(dirname($tokenPath))) {
              mkdir(dirname($tokenPath), 0700, true);
          }
          file_put_contents($tokenPath, json_encode($client->getAccessToken()));
      }
      return $client;
  }

  $client = getClient();
  /*
  function createMessage() {
    $msg = new Google_Service_Gmail_Message();
    $data = "";
    $data.= "To: nozoyama39@gmail.com\n"; //送信先
    $data.= "Cc: \n"; //CC
    $subject = "title"; //タイトル
    $data.= "Subject: ".mb_encode_mimeheader($subject, 'utf-8')."\n";
    $data.= "\n"; //改行２回でヘッダー部分を区別
    $body = <<<EOF
    [本文内容]
EOF;
    $data.= $body;
    $data = base64_encode($data); //base64エンコードする
    $data = strtr($data, '+/', '-_'); //サニタイジング
    $data = rtrim($data, '='); //最後の'='を除去
    $msg->setRaw($data); //データをセット
    return $msg;  //返却
  }
  //オブジェクト生成
  $service = new Google_Service_Gmail($client);
  //メッセージ作成
  $message = createMessage();
  //メッセージ送信
  $message = $service->users_messages->send("me", $message); */
?>