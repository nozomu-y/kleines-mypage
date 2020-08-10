<?php
  ini_set("display_errors",1);
  error_reporting(E_ALL);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  $mysqli = dbconnect();

  $status = ""; //デバッグ用

  //入力受け取り
  $ttcode = h($_GET['ttcode']);
  if($ttcode==NULL){
    $ttcode = 0;
  }

  //0枚か？→違ったら弾く(jsでも弾く)
  $stmt = $mysqli->prepare("SELECT amount FROM tp_TicketTotal WHERE ticketTypeCode = ?");
  $stmt->bind_param('i',$ttcode);
  $stmt->execute();
  $stmt->bind_result($am);
  if($row = $stmt->fetch()){
    $amount = $am;
  }else{
    $amount = -1;  //存在しないticketTypeCode
  }
  $stmt->close();
  if($amount == -1){
    //ticketTypeCodeが存在しない時、不正な操作として返す
    $_SESSION['tp_status'] = "invalidArgs";
    $status .= "invalidArgs";
  }else if($amount > 0){
    //チケットが存在している時は削除しない
    $_SESSION['tp_status'] = "existTicket";
    $status .= "existTicket";
  }else if($amount==0){
    //削除する
    $stmt = $mysqli->prepare("DELETE FROM tp_TicketTotal WHERE ticketTypeCode = ?");
    $stmt->bind_param('i',$ttcode);
    if($stmt->execute()){
      $status .= "SucDelTicTot";
    }else{
      $status .= "FailDelTicTot";
    }
    $_SESSION['tp_status'] = "deleteAssign";
    $stmt->close();
  }

  echo "<!--$status-->";
  
?>