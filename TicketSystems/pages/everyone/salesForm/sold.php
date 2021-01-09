<?php 
  require_once TP_ROOT."/include/orders/orderHandler.php";
  $id = $USER->id;
  $amount = $_POST['amount'];
  $lname_guest = $_POST['lname-guest'];
  $fname_guest = $_POST['fname-guest'];
  $lname_kana_guest = $_POST['lname-kana-guest'];
  $fname_kana_guest = $_POST['fname-kana-guest'];
  $amount_guest = $_POST['amount-guest'];
  $price_guest = $_POST['price-guest'];

  //通常の販売の分と、預かりの分で分ける
  $amount_sold = $amount; //通常販売分の枚数
  for($i=0; $i<count($lname_guest); $i++){
    $orderID = insertOrder($id, 5, $amount_guest[$i], $mysqli);  //預かりの分をtp_Ordersに挿入
    insertReserve($orderID, $lname_guest[$i], $fname_guest[$i], $lname_kana_guest[$i], $fname_kana_guest[$i], $price_guest[$i], $mysqli);  //預かりの情報をtp_Reservesに挿入
    $amount_sold -= $amount_guest[$i];
  }

  if($amount_sold > 0){
    insertOrder($id, 2, $amount_sold, $mysqli);  //通常の販売の分をtp_Ordersに挿入
    updateTicketAmount($id, 2, $amount_sold, $mysqli);  //tp_MemberTicketを更新
  }

  