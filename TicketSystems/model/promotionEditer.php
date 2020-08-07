<?php
  //update
  //groupName,date_determined,year,month,day,orderID
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSession();
  $mysqli = dbconnect();
  
  $groupName = $_POST['groupName'];
  $orderID = $_POST['orderID'];
  $date_determined = $_POST['date_determined'];
  $year = $_POST['year'];
  $month = $_POST['month'];
  $day = $_POST['day'];
  $status = $groupName;
  if($date_determined == 1){
    $status .= "<!dateDet>";
    $stmt = $mysqli->prepare("UPDATE tp_Promotions SET groupName = ?,date = ? WHERE orderID = ?");
    $targetDay = $year."-".$month."-".$day;
    $stmt->bind_Param('ssi',$groupName,date("Y-m-d",strtotime($targetDay)),$orderID);
  }else{
    $status .= "<!dateNotDet>";
    $stmt = $mysqli->prepare("UPDATE tp_Promotions SET groupName = ?,date = ? WHERE orderID = ?");
    $null = NULL;
    $stmt->bind_Param('ssi',$groupName,$null,$orderID);
  }
  $result = $stmt->execute();
  if(!$result){
    $status .= "<!--failtoInsertOrders:$mysqli->error-->";
  }else{
    $status .= "<!--succeedToInsertOrders -->";
  }
  $stmt->close();
  $mysqli->close();
?>