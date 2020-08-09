<?php
  /**
   * チケットを持っている人のpersonID,枚数を全て取得
   * 未解決オーダーの枚数を人ごとにgroupingして算出
   * have-未解決和の分をamountとして、returnの注文をその人のオーダーとして発注
   * 
   */

  ini_set("display_errors",1);
  error_reporting(E_ALL);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSecretSession();
  $mysqli = dbconnect();

  $status = ""; //for debug

  //全団員のpersonIDを取得
  $q1 = "SELECT personID FROM tp_MemberTickets";
  $result = $mysqli->query($q1);
  if($result==NULL){
    $status .= "-FailSelAllMem";
  }
  $members = [];
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $members[(int)$row['personID']] = (int)$row['personID'];
  }
  //結果セットを解放
  $result->free();

  //チケットを持ってる人のpersonIDと枚数を取得
  $q1 = "SELECT personID,have FROM tp_MemberTickets";
  $result = $mysqli->query($q1);
  if($result==NULL){
    $status .= "-FailSelMemTic";
  }
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    if((int)$row['have']>0){
      $members[(int)$row['personID']] = (int)$row['have'];  //上書き
    }else{
      $members[(int)$row['personID']] = 0;
    }
  }
  //結果セットを解放
  $result->free();


  //未解決オーダーの枚数の和を取得
  $q2 = "SELECT personID, sum(amount) AS sum_amount,sum(response) AS sum_response FROM tp_Orders WHERE finishFlag = 0 AND (orderTypeID = 3 OR orderTypeID = 5 OR orderTypeID = 7) GROUP BY personID";
  $result = $mysqli->query($q2);
  if($result==NULL){
    $status .= "-FailSelSums";
  }
  //personIDをindexとする配列に、未解決枚数の和を格納
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $members[(int)$row['personID']] -= (int)($row['sum_amount'] - $row['sum_response']);
  }
  //結果セットを解放
  $result->free();
  

  //返却希望オーダーを全員分発注
  //xxx ここを直す
  for($i=1;$i<=count($members);$i++){
    if($members[$i] > 0){
      $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,orderTime,finishFlag) VALUES (?,?,?,?,?)");
      $finishFlag = 0;
      $timeStamp = date("Y-m-d H:i:s");
      $orderTypeID = 3; //want_return
      $stmt->bind_param('iiisi',$i,$orderTypeID,$members[$i],$timeStamp,$finishFlag);
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to insert tp_Orders : $mysqli->error -->";
      }else{
        echo "<!-- succeed to insert tp_Orders -->";
      }
      $stmt->close();
    }
  }

  dbclose($mysqli);
  header("Location: ".SERVER."/view/secret/finishSettings.php");
  exit();
?>