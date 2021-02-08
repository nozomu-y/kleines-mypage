<?php 
  /**
   * 全てのticketTypeを挿入していく関数
   * @param ticketType チケット種別の名前が入っている配列
   * @param ticketTypeAmount チケット種別ごとの枚数が入っている配列
   * @param mysqli 
   */
  function configureTickets($ticketType, $ticketTypeAmount, $mysqli){
    for($i=0; $i<count($ticketTypeAmount); $i++){
      //既に存在するチケット種別を取得
      $result = $q_select = "SELECT * FROM tp_TicketTotal";
      $tuples_db = [];
      while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $tuples_db[] = $row;
      }
      $result->free();
      $values_db = array_column($tuples_db, 'ticketTypeValue');  //存在するticketTypeValueを配列で取得
      //ticketType[$i]がtuples_dbに存在している場合、UPDATE
      if(in_array($ticketType[$i], $values_db)){
        //DBでのticketTypeCodeを取得
        $index = array_search($ticketType[$i], $values_db);
        $ttc = $tuples_db[$index]["ticketTypeCode"];
        $isDefault = $tuples_db[$index]["isDefault"];
        //update
        $stmt_update;
        if($isDefault){
          //枚数のみ変更
          $stmt_update = $mysqli->prepare("UPDATE tp_TicketTotal SET amount=? WHERE ticketTypeCode = ?");
          $stmt_update->bind_param('ii', $ticketTypeAmount[$i], $ttc);
        }else{
          //枚数、名前を変更
          $stmt_update = $mysqli->prepare("UPDATE tp_TicketTotal SET ticketTypeValue=?, amount=? WHERE ticketTypeCode = ?");
          $stmt_update->bind_param('sii',$ticketType[$i], $ticketTypeAmount[$i], $ttc);
        }
        $res_exec = $stmt_update->execute();
        $stmt_update->close();
      }else{  //tuples_dbに存在しない場合、INSERT
        $stmt_insert = $mysqli->prepare("INSERT INTO tp_TicketTotal (ticketTypeValue, amount) VALUES (?,?)");
        $stmt_insert->bind_param('si',$ticketType[$i], $ticketTypeAmount[$i]);
        $res_exec = $stmt_insert->execute();
        $stmt_insert->close();
      }
    }
  }

  