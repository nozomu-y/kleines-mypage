<?php 
  require_once TP_ROOT."/include/orders/orderHandler.php";
  $id = $USER->id;
  $amount = $_POST['amount'];
  $orderTypeID = $_GET['orderTypeID'];
  //request, want_return, cancel共通で、tp_Ordersに挿入
  insertOrder($id, $orderTypeID, $amount, $mysqli);
  //cancelの場合のみ、tp_MemberTicketsを更新
  if($orderTypeID == 6){
    updateTicketAmount($id, $orderTypeID, $amount, $mysqli);
  }
