<?php 
  require_once TP_ROOT."/include/orders/orderHandler.php";
  $id = $USER->id;
  $amount = $_POST['amount'];
  $orderTypeID = $_GET['orderTypeID'];
  submitOrder($id, $orderTypeID, $amount, $mysqli);