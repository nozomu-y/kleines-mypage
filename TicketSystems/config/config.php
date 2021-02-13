<?php
  /**
   * TicketSystemsのモードを制御する定数
   * MODE == 'develop' : ローカルの開発環境
   * MODE == 'staging' : ステージング環境(サーバーでのテストなど、本番に近いテスト環境)
   * MODE == 'production' : 本番環境
   */
  define('MODE', 'develop');

  /* ヘッダーやタブに表示されるページ名 */
  define('SITE_NAME', 'tiCKetam');
  /* 権限を表す数字の設定 */
  define('NO_PERM_NUM', 999); //一般団員
  define('PR_CHIEF_PERM', 10);  //渉外チーフ権限(10番台は渉外チーフ。複数使い分ける場合は適宜設定してくださいx)
  define('PR_MEMBER_PERM', 20); //渉外権限
  //複数使い分ける場合は適宜設定してください。MAX_CHIEF_PERM < X <= MAX_PR_PERMの範囲で渉外内でも複数設定可能。例えば運営に一部の権限を持たせるとか。
  define('MAX_CHIEF_PERM', 19); 
  define('MAX_PR_PERM', 29);
  
  /* secret以下のページにアクセスするためのパスワード(適宜変えたり暗号化したりしてください) */
  define('SECRET_PASS', '09yampapa26');

  //rootの設定
  define('TP_ROOT', __DIR__ .'/..');
  
  /* モードごとのパス設定(本番環境や開発環境に合わせて変更してください) */
  if(MODE === 'develop'){
    // settings for mysql database
    define("DB_HOST", "localhost");
    define("DB_USER", "yakou1000");
    define("DB_PASS", "YakouPhpDev");
    define("DB_NAME", "PRPage");
    //server
    define('TP_SERVER', '//'. $_SERVER['SERVER_NAME'].'/TicketSystems/kleines-mypage/TicketSystems');
  }else if(MODE === "staging"){
    //settings for mysql database
    /* 本番環境とステージング環境でDBを変える場合、接続情報を保持(tp_initも編集する)
    define("DB_HOST", "");
    define("DB_USER", "");
    define("DB_PASS", "");
    define("DB_NAME", "");
    */
    //server
    define('TP_SERVER', "chorkleines.com" . "/member/mypage" . "/TicketSystems");
  }else if(MODE === "production"){
    //DBで使う定数はCommon/dbconnectでConfigを読み込むときに定義される
    define('TP_SERVER', "chorkleines.com" . "/member/mypage" . "/TicketSystems");
    
  }else{
    echo("config.MODE is invalid");
    exit();
  }

?>