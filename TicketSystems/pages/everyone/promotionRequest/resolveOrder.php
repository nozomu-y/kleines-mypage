<?php
  require_once TP_ROOT."/include/orders/orderHandler.php";
  $id = $USER->id;
  $amount = htmlspecialchars($_POST['amount']);
  $orderTypeID = 4; //want_promotion
  $groupName = htmlspecialchars($_POST['groupName']);
  $dateStr = htmlspecialchars($_POST['date']);
  if($dateStr == ""){
    $date = null;
  }else{
    $date = date("Y-m-d",strtotime($dateStr));
  }
  
  //tp_Ordersに挿入し、挿入時のorderIDを取得
  $orderID = insertOrder($id, $orderTypeID, $amount, $mysqli);
  //tp_Promotionsに挿入
  insertPromotion($orderID, $groupName, $date, $mysqli);
