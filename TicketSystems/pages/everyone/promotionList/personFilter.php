<?php 
  /**
   * 不正な操作が行われたことを示してリダイレクトする関数
   */
  function invalid(){
    $_SESSION['tp_status'] = "invalid-page";
    header("Location: list.php");
    exit();
  }

  /**
   * @param orderID GETで取得したorderID
   * @param id USER->id
   * @param mysqli mysqliオブジェクト
   */
  function personFilter($orderID, $id, $mysqli){
    //orderIDを取得する
    if(!isset($orderID)){ //orderIDが入力されていなかった場合
      invalid();
    }
    if(!is_numeric($orderID)){  //数字以外のorderIDが入力されていた場合
      invalid();
    }

    //入力されたorderIDからpromotionの内容を取得
    $stmt_promotion = $mysqli->prepare(
      "SELECT id FROM tp_Promotions 
      INNER JOIN tp_Orders USING(orderID) 
      WHERE orderID = ? AND tp_Promotions.finishFlag = 0");
    $stmt_promotion->bind_param('i', $orderID);
    $stmt_promotion->execute();
    $stmt_promotion->bind_result($personID);
    $result = $stmt_promotion->fetch();
    if($result==null || $result == false){
      invalid();
    }
    $stmt_promotion->close();

    //orderと違う人だった場合、listに飛ばす
    if($personID != $id){
      invalid();
    }
  }
  