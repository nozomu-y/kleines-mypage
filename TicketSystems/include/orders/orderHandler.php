<?php

  /**
   * tp_Ordersテーブルに挿入する関数
   */
  function insertOrder($personID, $orderTypeID, $amount, $mysqli){
    $stmt_insert = $mysqli->prepare(
      "INSERT INTO tp_Orders (id, orderTypeID, amount, response, orderTime, finishFlag, finishTime)
       VALUES (?, ?, ?, ?, ?, ?, ?)");
    $finishFlag = getFinishFlag($orderTypeID, $amount);
    $response = 0;
    $orderTime = date("Y-m-d H:i:s");
    $finishTime = null;
    if($finishFlag == 1){
      $response = $amount;
      $finishTime = $orderTime;
    }
    $stmt_insert->bind_param('iiiisis', $personID, $orderTypeID, $amount, $response, $orderTime, $finishFlag, $finishTime);
    $result = $stmt_insert->execute();
    if(!$result){
      echo($mysqli->error);
    }
    $stmt_insert->close();
    return $mysqli->insert_id;
  }

  /**
   * 
   * @param orderID 対応するtp_Orders中のorderID
   * @param groupName 訪問先の団体名
   * @param date dateオブジェクト、またはnull。入力前に選択してください。strtotime(null)は"1970-01-01 00:00:00"を返します
   * @param mysqli mysqliオブジェクト
   */
  function insertPromotion($orderID, $groupName, $date, $mysqli){
    $stmt_insert = $mysqli->prepare("INSERT INTO tp_Promotions (orderID, groupName, date) VALUES (?, ?, ?)");
    $stmt_insert->bind_param('iss', $orderID, $groupName, $date);
    if(!$stmt_insert->execute()){
      echo($mysqli->error);
      exit();
    }
    $stmt_insert->close();
  }

  /**
   * 
   */
  function insertReserve($orderID, $lname, $fname, $lname_kana, $fname_kana, $price, $mysqli){
    $stmt_insert = $mysqli->prepare(
      "INSERT INTO tp_Reserves (orderID, lastName, firstName, lastNameKana, firstNameKana, price, visitFlag)
       VALUES (?, ?, ?, ?, ?, ?, ?)");
    $visitFlag = 0;
    $stmt_insert->bind_param('issssii', $orderID, $lname ,$fname, $lname_kana, $fname_kana, $price, $visitFlag);
    if(!$stmt_insert->execute()){
      echo($mysqli->error);
      exit();
    }
    $stmt_insert->close();
  }

  /**
   * tp_TicketTotalをupdateする関数
   * @param ticketTypeCode tp_TicketTotal.ticketTypeCode
   * @param operator "+"または"-"を文字列で指定
   * @param amount チケットの枚数
   * @param mysqli mysqliオブジェクト
   */
  function updateTicketTotal($ticketTypeCode, $operator, $amount, $mysqli){
    if($amount == 0) return;
    if(strcmp($operator, "+") == 0){
      $stmt_TicTot = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = ?");
    }else if(strcmp($operator, "-") == 0){
      $stmt_TicTot = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = ?");
    }else{
      //error
      echo("orderHandler.updateTicketTotal : invalid operator");
      exit();
    }
    $stmt_TicTot->bind_param('ii', $amount, $ticketTypeCode);
    if(!$stmt_TicTot->execute()){
      echo($mysqli->error);
      exit();
    }
    $stmt_TicTot->close();
  }

  /**
   * tp_MemberTicketsをupdateする関数
   * @param type "have"または"sold"を文字列で指定する
   * @param operator "+"または"-"を文字列で指定する
   * @param personID 更新する団員のid
   * @param amount チケットの枚数
   * @param mysqli mysqliオブジェクト
   */
  function updateMemberTickets($type, $operator, $personID, $amount, $mysqli){
    if($amount == 0) return;
    if((strcmp($type, "have") == 0) && (strcmp($operator, "+") == 0)){
      $stmt_MemTic = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have + ? WHERE id = ?");
    }else if((strcmp($type, "have") == 0) && (strcmp($operator, "-") == 0)){
      $stmt_MemTic = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have - ? WHERE id = ?");
    }else if((strcmp($type, "sold") == 0) && (strcmp($operator, "+") == 0)){
      $stmt_MemTic = $mysqli->prepare("UPDATE tp_MemberTickets SET sold = sold + ? WHERE id = ?");
    }else if((strcmp($type, "sold") == 0) && (strcmp($operator, "-") == 0)){
      $stmt_MemTic = $mysqli->prepare("UPDATE tp_MemberTickets SET sold = sold - ? WHERE id = ?");
    }else{
      //error
      echo("orderHandler.updateMemberTickets : invalid type($type) or operator($operator)");
      exit();
    }
    $stmt_MemTic->bind_param('ii', $amount, $personID);
    if(!$stmt_MemTic->execute()) echo($mysqli->error);
    $stmt_MemTic->close();
  }

  /**
   * 
   */
  function updateTicketAmount($personID, $orderTypeID, $amount, $mysqli){
    if($amount == 0) return;
    switch($orderTypeID){
      case 1: //request
      case 4: //want_promotion
        updateTicketTotal(1, "-", $amount, $mysqli);   //渉外所持を減らす
        updateTicketTotal(3, "+", $amount, $mysqli);  //団員所持を増やす
        updateMemberTickets("have", "+", $personID, $amount, $mysqli);  //団員のhaveを増やす
        break;
      case 3: //want_return
      case 7: //finish_promotion
        updateTicketTotal(1, "+", $amount, $mysqli);  //渉外所持を増やす
        updateTicketTotal(3, "-", $amount, $mysqli);  //団員所持を減らす        
        updateMemberTickets("have", "-", $personID, $amount, $mysqli);  //団員のhaveを減らす
        break;
      case 5: //sold_with_reserve
        updateTicketTotal(3, "-", $amount, $mysqli);  //団員所持を減らす
        updateTicketTotal(2, "+", $amount, $mysqli);  //預かり用回収済みを増やす
        //団員のhaveを減らし, soldを増やす
        updateMemberTickets("have", "-", $personID, $amount, $mysqli);
        updateMemberTickets("sold", "+", $personID, $amount, $mysqli);
        break;
      case 2: //sold
        updateTicketTotal(3, "-", $amount, $mysqli);  //団員所持を減らす
        updateTicketTotal(4, "+", $amount, $mysqli);  //団員販売済みを増やす
        //団員のhaveを減らし、soldを増やす
        updateMemberTickets("have", "-", $personID, $amount, $mysqli);
        updateMemberTickets("sold", "+", $personID, $amount, $mysqli);
        break;
      case 6: //cancel
        updateTicketTotal(3, "+", $amount, $mysqli);  //団員所持を増やす
        updateTicketTotal(4, "-", $amount, $mysqli);  //団員販売済みを減らす
        //団員のhaveを増やし、soldを減らす
        updateMemberTickets("have", "+", $personID, $amount, $mysqli);
        updateMemberTickets("sold", "-", $personID, $amount, $mysqli);
        break;
      case 10:  //cancel_reserve
        /*
        TODO:
        cancel_reserveはOrderで処理するのか不明なので、一旦保留。
        未処理オーダーの取り消しの機構で処理しないと、どのreserveをcancelするのか捕捉不能だと思う。
        未処理オーダーの取り消しの機構を作成する際に改めて検討する。

        updateTicketTotal(1, "+", $amount, $mysqli);  //渉外所持を増やす
        updateTicketTotal(2, "-", $amount, $mysqli);  //預かり用回収済みを減らす
        */
        break;
      case 8: //transfar_receive
        updateMemberTickets("have", "+", $personID, $amount, $mysqli);  //団員のhaveを増やす
        break;
      case 9: //transfar_give
        updateMemberTickets("have", "-", $personID, $amount, $mysqli);  //団員のhaveを減らす
        break;
      default:
        //error
        echo("orderHandler.updateTicketAmount : invalid orderTypeID($orderTypeID)");
        exit();
    }
  }

  function transferTicket($give_id, $receive_id, $amount, $mysqli){
    if($amount == 0) return;
    insertOrder($give_id, 9, $amount, $mysqli);
    insertOrder($receive_id, 8, $amount, $mysqli);
    updateTicketAmount($give_id, 9, $amount, $mysqli);
    updateTicketAmount($receive_id, 8, $amount, $mysqli);
  }

  /**
   * finishFlagの値を返す(=渉外が関与するかどうかを示す)関数
   * 渉外が関与しない場合1を、関与する場合0を返す
   */
  function getFinishFlag($orderTypeID, $amount){
    if($amount == 0){
      return 1;
    }
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
      //case 10:  //cancel_reserve
        /*
        cancel_reserveはOrderで処理するのか不明なので、一旦保留。
        未処理オーダーの取り消しの機構で処理しないと、どのreserveをcancelするのか捕捉不能だと思う。
        未処理オーダーの取り消しの機構を作成する際に改めて検討する。
        */
        return 0;
      default:
        return -1;
    }
  }

  /**
   * あるオーダーに対処する関数
   * @param personID 対処した団員のid
   * @param orderID 対処したオーダーのid
   * @param amount 対処した枚数
   * @param mysqli mysqliオブジェクト 
   */
  function responseOrder($personID, $orderID, $amount, $mysqli){
    $timeStamp = date("Y-m-d H:i:s");
    //そのオーダーの情報を取得
    $stmt_select = $mysqli->prepare("SELECT id, orderTypeID, amount, response FROM tp_Orders WHERE orderID = ?");
    $stmt_select->bind_param('i', $orderID);
    $stmt_select->execute();
    $stmt_select->bind_result($order_person, $orderTypeID, $order_amount, $order_response);
    $result = $stmt_select->fetch();
    if($result == null || $result == false){
      //error
    }
    //そのオーダーの全ての枚数に対応したのかを確認
    $order_rest = $order_amount - $order_response;  //そのオーダーの対応が必要な枚数
    $finishFlag = 0;
    if($order_rest == $amount){
      $finishFlag = 1;
    }
    $stmt_select->close();

    //tp_Ordersのresponseを増やす
    if($finishFlag == 1){
      $finishTime = $timeStamp;
      $stmt_update = $mysqli->prepare(
        "UPDATE tp_Orders SET response = response + ?, finishFlag = ?, finishTime = ? WHERE orderID = ?");
      $stmt_update->bind_param('iisi', $amount, $finishFlag, $finishTime, $orderID);
    }else if($finishFlag == 0){
      $stmt_update = $mysqli->prepare("UPDATE tp_Orders SET response = response + ? WHERE orderID = ?");
      $stmt_update->bind_param('ii', $amount, $orderID);
    }
    $result = $stmt_update->execute();
    $stmt_update->close();
    
    //tp_Responsesにタプルを挿入
    $responseTime = $timeStamp;
    $stmt_insert = $mysqli->prepare(
      "INSERT INTO tp_Responses (orderID, id, amount, responseTime) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param('iiis', $orderID, $personID, $amount, $responseTime);
    $result = $stmt_insert->execute();
    $stmt_insert->close();

    //tp_MemberTickets、tp_TicketTotalを更新
    updateTicketAmount($order_person, $orderTypeID, $amount, $mysqli);
  }