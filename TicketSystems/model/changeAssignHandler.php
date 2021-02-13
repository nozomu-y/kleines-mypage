<?php
  //ini_set("display_errors",1);
  //error_reporting(E_ALL);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  $mysqli = dbconnect();

  $status = ""; //デバッグ用

  $amount = $_POST['amount'];
  $addrem = $_POST['AddRem'];
  $sum = 0; //渉外所持の+-のずれ
  //フォームが送信されているかを調べる
  $submitted = FALSE;
  foreach($amount as $amo){
    if(!empty($amo)){
      $submitted = TRUE;
      break;
    }
  }
  if($submitted){
    //チケット区分を取得
    $q1 = "SELECT ticketTypeCode FROM tp_TicketTotal WHERE ticketTypeCode >= 5";
    $result = $mysqli->query($q1);
    while($row = $result->fetch_array(MYSQLI_ASSOC)){
      $rows[] = (int)$row['ticketTypeCode'];
    }
    //各チケット区分を更新する
    foreach($rows as $i){
      $stmt = "";
      if(strcmp($addrem[$i],"add")==0){
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = ?");
        $sum += (int)$amount[$i];
      }else if(strcmp($addrem[$i],"rem")==0){
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = ?");
        $sum -= (int)$amount[$i];
      }
      $am = h((int)$amount[$i]);
      $stmt->bind_param('ii',$am,$i);
      if($stmt->execute()){
        $status .= "-SucUpd$i";
      }else{
        $status .= "-FailUpd$i";
      }
      $stmt->close();
    }
    //渉外所持を更新する
    if($sum > 0){
      $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 1");
      $stmt->bind_param('i',$sum);
    }else if($sum < 0){
      $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 1");
      $sum *= -1;
      $stmt->bind_param('i',$sum);
    }
    if($sum != 0){
      if($stmt != NULL && $stmt->execute()){
        $status .= "-SucUpd1";
      }else{
        $status .= "-FailUpd1";
      }
      $stmt->close();
    }
  }
  dbclose($mysqli);
  echo $status;
?>