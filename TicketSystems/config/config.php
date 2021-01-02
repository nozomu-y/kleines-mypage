<?php
  define('MODE','test');

  define('SITE_NAME','CK_TicketSystem');
if(strcmp(MODE,'test')==0){
  define('TP_ROOT',$_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems');
  define('TP_SERVER','//'.$_SERVER['SERVER_NAME'].'/TicketSystems/kleines-mypage/TicketSystems');
  
}else if(strcmp(MODE,"real")==0){
  define('TP_ROOT', WEB_DOMAIN . MYPAGE_ROOT . "/TicketSystems");
  define('TP_SERVER', TP_ROOT);
}

?>