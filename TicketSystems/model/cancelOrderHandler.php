<?php
  //ini_set("display_errors",1);
  //error_reporting(E_ALL);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
  $mysqli = dbconnect();
  
  $orderID = h($_POST['orderID']);
  $status = ""; //デバッグ用

  /**
   * responseが0か1以上かで分岐
   * 0のとき、普通に削除で終了
   * 1以上の時、responseの分までは完了済みにして、削除は行わない
   */

  //枚数とresponseを取得
  $stmt = $mysqli->prepare('SELECT amount,response FROM tp_Orders WHERE orderID = ?');
  $stmt->bind_param('i',$orderID);
  $stmt->execute();
  $stmt->bind_result($amount,$response);
  $result = $stmt->fetch();
  if($result == NULL){
    $status .= "-FailSelOrders";
  }else{
    $status .= "-SucSelOrders";
  }
  $stmt->close();

  if($response==0){
    //削除
    $stmt = $mysqli->prepare("DELETE FROM tp_Orders WHERE orderID = ? AND finishFlag = 0"); //finFlgを指定することで一貫性を保つ
    $stmt->bind_param('i',$orderID);
    if($stmt->execute()){
      $status .= "-SucDelOrder";
    }else{
      $status .= "-FailDelOrder";
    }
    $stmt->close();
  }else if($response>0){
    $yet = $amount - $response; //未完了分
    $timeStamp = date("Y-m-d H:i:s"); //完了日時
    //responseまでamountを減らす
    $stmt = $mysqli->prepare("UPDATE tp_Orders SET amount = amount - ?, finishFlag = 1, finishTime = ? WHERE orderID = ? AND finishFlag = 0");  //finFlgを指定することで一貫性を保つ
    $stmt->bind_param('isi',$yet,$timeStamp,$orderID);
    if($stmt->execute()){
      $status .= "-SucUpdOrders";
    }else{
      $status .= "-FailUpdOrders";
    }
    $stmt->close();
  }
  //sessionを更新し、historyに飛ばす
  dbclose($mysqli);
  $_SESSION['mypage_status'] = "cancelOrder";
  header("Location: ".SERVER."/view/everyone/history.php?status=$status");
  exit();
?>