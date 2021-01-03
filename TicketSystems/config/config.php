<?php
  define('MODE', 'test');

  define('SITE_NAME', 'CK_TicketSystem');
  define('NO_PERM_NUM', 999);
  define('MAX_CHIEF_PERM', 19);
  define('MAX_PR_PERM', 29);
  define('PR_CHIEF_PERM', 10);
  define('PR_MEMBER_PERM', 20);
  define('SECRET_PASS', '09yampapa26');
if(strcmp(MODE,'test')==0){
  define('TP_ROOT', $_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems');
  define('TP_SERVER', '//'.$_SERVER['SERVER_NAME'].'/TicketSystems/kleines-mypage/TicketSystems');
  
}else if(strcmp(MODE, "real")==0){
  define('TP_ROOT', '//' . WEB_DOMAIN . MYPAGE_ROOT . "/TicketSystems");
  define('TP_SERVER', TP_ROOT);
}

?>