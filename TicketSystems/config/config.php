<?php
  /**
   * TicketSystemsのモードを制御する定数
   * MODE == 'test' : テストモード
   * MODE == 'real' : 本番環境
   */
  define('MODE', 'test');

  /* ヘッダーやタブに表示されるページ名 */
  define('SITE_NAME', 'CK_TicketSystem');
  /* 権限を表す数字の設定 */
  define('NO_PERM_NUM', 999); //一般団員
  define('PR_CHIEF_PERM', 10);  //渉外チーフ権限(10番台は渉外チーフ。複数使い分ける場合は適宜設定してくださいx)
  define('PR_MEMBER_PERM', 20); //渉外権限
  //複数使い分ける場合は適宜設定してください。MAX_CHIEF_PERM < X <= MAX_PR_PERMの範囲で渉外内でも複数設定可能。例えば運営に一部の権限を持たせるとか。
  define('MAX_CHIEF_PERM', 19); 
  define('MAX_PR_PERM', 29);
  
  /* secret以下のページにアクセスするためのパスワード(適宜変えたり暗号化したりしてください) */
  define('SECRET_PASS', '09yampapa26');
  
  /* モードごとのパス設定(本番環境や開発環境に合わせて変更してください) */
  if(strcmp(MODE,'test')==0){
    define('TP_ROOT', $_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems');
    define('TP_SERVER', '//'.$_SERVER['SERVER_NAME'].'/TicketSystems/kleines-mypage/TicketSystems');
    
  }else if(strcmp(MODE, "real")==0){
    define('TP_ROOT', '//' . WEB_DOMAIN . MYPAGE_ROOT . "/TicketSystems");
    define('TP_SERVER', TP_ROOT);
  }

?>