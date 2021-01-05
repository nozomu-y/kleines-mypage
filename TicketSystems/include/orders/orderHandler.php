<?php
  /**
   * tp_Ordersテーブルに挿入する関数
   */
  function submitOrder($id, $orderTypeID, $amount, $mysqli){
    $stmt_insert = $mysqli->prepare(
      "INSERT INTO tp_Orders (id, orderTypeID, amount, response, orderTime, finishFlag, finishTime) VALUES (?,?,?,?,?,?,?)");
    $finishFlag = getFinishFlag($orderTypeID);
    $response = 0;
    $orderTime = date("Y-m-d H:i:s");
    $finishTime = null;
    if($finishFlag == 1){
      $response = $amount;
      $finishTime = $orderTime;
    }
    $stmt_insert->bind_param('iiiisis', $id, $orderTypeID, $amount, $response, $orderTime, $finishFlag, $finishTime);
    $result = $stmt_insert->execute();
    if(!$result){
      echo($mysqli->error);
    }
    $stmt_insert->close();
  }

  /**
   * finishFlagの値を返す(=渉外が関与するかどうかを示す)関数
   * 渉外が関与しない場合1を、関与する場合0を返す
   */
  function getFinishFlag($orderTypeID){
    switch($orderTypeID){
      case 2: //sold
      case 6: //cancel
      case 8: //transfar_receive
      case 9: //transfar_give
        return 1;
      case 1: //request
      case 3: //want_return
      case 4: //want_promotion
      case 5: //sold_with_reserve
      case 7: //finish_promotion
      case 10:  //cancel_reserve
        return 0;
      default:
        return -1;
    }
  }