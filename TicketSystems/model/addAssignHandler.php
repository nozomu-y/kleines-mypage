<?php
  ini_set("display_errors",1);
  error_reporting(E_ALL);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  $mysqli = dbconnect();

  $status = ""; //デバッグ用

  //入力受け取り
  $ttname = h($_POST['ticketTypeName']);
  $amount = h((int)$_POST['amount']);

  //ticketTypeの挿入
  $stmt = $mysqli->prepare("INSERT INTO tp_TicketTotal (ticketTypeValue,amount) VALUES (?,?)");
  $stmt->bind_param('si',$ttname,$amount);
  if($stmt->execute()){
    $status .= "SucInsTicTot";
  }else{
    $status .= "FailInsTicTot";
  }
  $stmt->close();

  //渉外所持の枚数の調整
  $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 1");
  $stmt->bind_param('i',$amount);
  if($stmt->execute()){
    $status .= "-SucUpdTicTot";
  }else{
    $status .= "-FailUpdTicTot";
  }
  $stmt->close();



?>