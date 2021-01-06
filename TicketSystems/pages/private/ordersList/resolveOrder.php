<?php
  //切り分けてからresponseにひたすら投げる
  require_once TP_ROOT."/include/orders/orderHandler.php";

  //内容取得
  $orderIDs = [];
  $orderTypeID = $_GET['orderTypeID'];
  if(isset($_POST['orderID']) && is_array($_POST['orderID'])){
    $orderIDs = $_POST['orderID'];
  }
  //手続きごとにSQLを処理していく
  foreach($orderIDs as $orderID){
    $amount = $_POST['amount'.$orderID];
    responseOrder($USER->id, $orderID, $amount, $mysqli);
  }