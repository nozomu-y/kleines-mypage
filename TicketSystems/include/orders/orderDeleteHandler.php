<?php
  require_once __DIR__."/orderHandler.php";
  /**
   * tp_Ordersテーブルでの削除を行う関数
   */
  function deleteOrder($personID, $orderID, $orderTypeID, $mysqli){
    //orderIDから詳細情報を取得
    $stmt_detail = $mysqli->prepare("SELECT response FROM tp_Orders WHERE orderID = ?");
    $stmt_detail->bind_param('i', $orderID);
    $stmt_detail->execute();
    $stmt_detail->bind_result($response);
    $result = $stmt_detail->fetch();
    $stmt_detail->close();
    if($result == null || $result == false){  //error
      echo($mysqli->error);
      exit();
    }
    //orderTypeIDによっては、response>0ならばOrderを挿入
    switch($orderTypeID){
      case 1: //request
      case 3: //want_return
        break;
      case 4: //want_promotion
        if($response > 0){
          insertOrder($personID, 11, $response, $mysqli);
        }
        break;
      case 5: //sold_with_reserve
        if($response > 0){
          insertOrder($personID, 10, $response, $mysqli);
        }
        break;
      default:  //error
        echo("orderDeleteHandler.deleteOrder : invalid orderTypeID");
        exit();
    }
    //orderのdeleteFlag, deleteTimeを更新
    $deleteFlag = 1;
    $deleteTime = date("Y-m-d H:i:s");
    $stmt_update = $mysqli->prepare("UPDATE tp_Orders SET deleteFlag = ?, deleteTime = ? WHERE orderID = ?");
    $stmt_update->bind_param('isi', $deleteFlag, $deleteTime, $orderID);
    if(!$stmt_update->execute()){
      echo($mysqli->error);
      exit();
    }
  }

  /**
   * reservesからの削除を行う関数
   * @param orderID 削除するorderのorderID
   * @param mysqli mysqliオブジェクト
   */
  function deleteReserve($orderID, $mysqli){
    $stmt_delete = $mysqli->prepare("DELETE FROM tp_Reserves WHERE orderID = ?");
    $stmt_delete->bind_param('i', $orderID);
    if(!$stmt_delete->execute()){
      echo($mysqli->error);
      exit();
    }
  }
