<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
  $mysqli = dbconnect();
  
  /*
    tp_Reservesからタプル削除
    tp_Ordersにcancel_reserve
    if(tp_Orders.finishFlag){
      tp_MemberTickets.sold -=1;
      渉外のチケット枚数を増やす・移行命令を出す
    }else{
      終わり
    }
    header location:
  */

  //送信データ受け取り
  $personID = h($_SESSION['mypage_personID']);
  $orderID = h($_POST['orderID']);
  $amount = h($_POST['amount']);
  $response = h($_POST['response']);
  $timeStamp = date("Y-m-d H:i:s");
  $status = ""; //エラーチェック用

  //tp_Reservesからタプルを削除
  $stmt = $mysqli->prepare("DELETE FROM tp_Reserves WHERE orderID = ?");
  $stmt->bind_param('i',$orderID);
  $result = $stmt->execute();
  if($result){
    $status .= "SucDel";
  }else{
    $status .= "FailDel";
  }
  $stmt->close();

  //TODO 渉外に預かりチケットを渡す前にキャンセルしたらどうなるか？
  //tp_Ordersにcancel_detlete(orderTypeID=10)
  $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,orderTime,response,finishFlag,finishTime) VALUES (?,10,?,?,?,1,?)");
  $stmt->bind_param('iisis',$personID,$response,$timeStamp,$response,$timeStamp); //cancelは初期値でfinishFlag=1,finishTime,amountは入力値
  $result = $stmt->execute();
  if($result){
    $status .= "-SucIns";
  }else{
    $status .= "-FailIns";
  }
  $stmt->close();

  //渉外に渡していた枚数分、tp_MemberTicketsを変更
  if($response>0){
    $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET sold = sold - ? WHERE personID = ?");
    $stmt->bind_param('ii',$response,$personID);
    $result = $stmt->execute();
    if($result){
      $status .= "-SucUpd";
    }else{
      $status .= "-FailUpd";
    }
    $stmt->close();
    //tp_TicketTotalの更新
    //預かりを減らす
    $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 2");  //預かり用に回収済み
    $stmt->bind_param('i',$amount);
    $result = $stmt->execute();
    if(!$result){
      $status.= "-FailUpdTotal";
    }else{
      $status.= "-SucUpdTotal";
    }
    $stmt->close();

    //渉外所持を増やす
    //中間点検の時に枚数のズレを補正
    $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 1");  //団員所持
    $stmt->bind_param('i',$amount);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
    }
    $stmt->close();
    
  }else{
    //memo 渉外にまだ渡していない場合の処理
    //多分tp_Ordersから消したりとかそういうの。
  }

  //履歴一覧にリダイレクト
  $_SESSION["mypage_status"] = "succeed_delete";
  header("Location: ".SERVER."/view/everyone/history.php?status=".$status);
  dbclose($mysqli);
  exit();
?>