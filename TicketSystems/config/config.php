<?php
  define('MODE','test');

if(strcmp(MODE,'test')==0){
  define('DB_NAME','PRPage');
  define('DB_HOST','localhost');
  define('DB_USER','yakou1000');
  define('DB_PASS','YakouPhpDev');
  define('ROOT',$_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems');
  define('SERVER','//'.$_SERVER['SERVER_NAME'].'/TicketSystems/kleines-mypage/TicketSystems');
  define('SITE_NAME','CK_TicketSystem');
}else if(strcmp(MODE,"real")==0){
  require_once('/home/chorkleines/www/member/mypage/Core/config.php');
  define('ROOT','/home/chorkleines/www/member/mypage/TicketSystems');
  define('SERVER','/member/mypage/TicketSystems');
}

?>