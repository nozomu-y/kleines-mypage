<?php
  ini_set("display_errors",1);
  error_reporting(E_ALL);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSession();
  $mysqli = dbconnect();

  $status = ""; //デバッグ用

  //変更後データ受け取り
  $lname = h($_POST['lname']);
  $fname = h($_POST['fname']);
  $lnameKana = h($_POST['lnameKana']);
  $fnameKana = h($_POST['fnameKana']);
  $price = h($_POST['price']);
  $amount = h($_POST['amount']);
  $orderID = h($_POST['orderID']);

  //変更前の枚数のデータをDBで検索
	$stmt = $mysqli->prepare("SELECT amount,response,finishFlag,personID FROM tp_Reserves INNER JOIN tp_Orders USING(orderID) WHERE orderID = ?");
	$stmt->bind_param('i',$orderID);
	$stmt->execute();
	$stmt->bind_result($amount_ori,$response_ori,$finishFlag_ori,$personID);
	$result = $stmt->fetch();
	if(!$result){
	  $status .= "FailSelReserves:".$result->error;
	}
	$stmt->close();
  
  //tp_ReservesはそのままUPDATE
  $stmt = $mysqli->prepare("UPDATE tp_Reserves SET lastName = ?, firstName = ?, lastNameKana = ?, firstNameKana = ?, price = ? WHERE orderID = ?");
  $stmt->bind_param('ssssii',$lname,$fname,$lnameKana,$fnameKana,$price,$orderID);
  if($stmt->execute()){
    $status .= "-SucUpdReserve";
  }else{
    $status .= "-FailUpdReserve:$mysqli->error";
  }
  $stmt->close();
  //枚数が増えた場合か、枚数が減ったが対応済み枚数よりは多い場合、tp_Ordersのamount・finFlag・finTimeも更新
  if($amount > $amount_ori || ($amount < $amount_ori && $amount > $response_ori)){
    $stmt = $mysqli->prepare("UPDATE tp_Orders SET amount = ?, finishFlag = 0, finishTime = ? WHERE orderID = ?");
    $finTime = NULL;
    $stmt->bind_param('isi',$amount,$finTime,$orderID);
    if($stmt->execute()){
      $status .= "-SucUpdOrder1";
    }else{
      $status .= "-FailUpdOrder1";
    }
    $stmt->close();
  }else if($amount < $amount_ori && $amount <= $response_ori){ //枚数が減った場合で、新たなamountがすでに対応済みの枚数以下になった場合
    $delta = $response_ori - $amount;  //減らした結果、余分になった枚数
    $result = 2;
    if($finishFlag_ori){
      $stmt = $mysqli->prepare("UPDATE tp_Orders SET amount = ?, response = ? WHERE orderID = ?");
      $stmt->bind_param('iii',$amount,$amount,$orderID);
      $result = $stmt->execute();
    }else{
      $timeStamp = date("Y-m-d H:i:s"); //現在日時
      $stmt = $mysqli->prepare("UPDATE tp_Orders SET amount = ?, response = ?, finishFlag = 1, finishTime = ? WHERE orderID = ?");
      $stmt->bind_param('iisi',$amount,$amount,$timeStamp,$orderID);
      $result = $stmt->execute();
    }
    if($result==1){
      $status .= "-SucUpdOrder2";
    }else if($result==0){
      $status .= "-FailUpdOrder2:$mysqli->error";
    }else{
      $status .= "-NoTouchUpdOrder2";
    }
    $stmt->close();
    if($delta>0){
      //tp_TicketTotalも更新する
      $q1 = "UPDATE tp_TicketTotal SET amount = amount + $delta WHERE ticketTypeCode = 1";  //渉外所持を増やす
      $q2 = "UPDATE tp_TicketTotal SET amount = amount - $delta WHERE ticketTypeCode = 2";  //預かり用回収済みを減らす
      if($mysqli->query($q1)&&$mysqli->query($q2)){
        $status .= "-SucUpdTicTot";
      }else{
        $status .= "-FailUpdTicTot:$mysqli->error";
      }
      //tp_MemberTicketも更新する
      $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET sold = sold - ? WHERE personID = ?");
      $stmt->bind_param('ii',$delta,$personID);
      if($stmt->execute()){
        $status .= "-SucUpdMemTic";
      }else{
        $status .= "-FailUpdMemTic:$mysqli->error";
      }
    }
  }
  //sessionを更新し、historyへ戻す
  dbclose($mysqli);
  $_SESSION['mypage_status'] = "editReserve";
  header("Location: ".SERVER."/view/everyone/history.php?status=$status");
  exit();
?>