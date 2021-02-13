<?php
  //passwordを受け取り、合ってたらsession['tp_secret] = "OK"
  //間違ってたらindexにでも飛ばす
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  session_start();
  if (!isset($_SESSION["mypage_email"])) {  //未ログイン
    header("Location: ".SERVER."/view/signIn.php");
    exit();
  }
  //passと比較
  if(strcmp(h($_POST['pass']),"09Yampapa26")==0){
    $_POST = array();
    $_SESSION['tp_secret'] = "OK";
    $_SESSION['tp_status'] = "succeed";
    header("Location: ".SERVER."/view/secret/index.php");
    exit();
  }else{
    $_SESSION['tp_status'] = "noAdmin";
    header("Location: ".SERVER."/view/signIn.php");
    exit();
  }




?>