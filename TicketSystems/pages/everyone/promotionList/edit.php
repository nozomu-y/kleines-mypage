<?php
  $orderID = htmlspecialchars($_GET['orderID']);
  $groupName = htmlspecialchars($_POST['groupName']);
  $dateStr = htmlspecialchars($_POST['date']);
  if($dateStr == ""){
    $date = null;
  }else{
    $date = date("Y-m-d",strtotime($dateStr));
  }
  $stmt_update = $mysqli->prepare("UPDATE tp_Promotions SET groupName = ?, date = ? WHERE orderID = ?");
  $stmt_update->bind_param('ssi', $groupName, $date, $orderID);
  if(!$stmt_update->execute()){
    echo($mysqli->error);
    exit();
  }
  $stmt_update->close();