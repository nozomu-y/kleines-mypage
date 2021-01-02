<?php 
  $sum = $_POST["sumAmount"]; //scala
  $ticketType = $_POST["ticketType"]; //array
  $ticketTypeAmount = $_POST["ticketTypeAmount"];  //array

  //全てのものを挿入or更新していく
  for($i=0; $i<count($ticketTypeAmount); $i++){
    if($i>=0 && $i<=3){
      $ttc = $i + 1;
      $stmt_update = $mysqli->prepare("UPDATE tp_TicketTotal SET ticketTypeValue=?, amount=? WHERE ticketTypeCode = ?");
      $stmt_update->bind_param('sii',$ticketType[$i],$ticketTypeAmount[$i],$ttc);
      $res_exec = $stmt_update->execute();
      $stmt_update->close();
    }else{
      $stmt_insert = $mysqli->prepare("INSERT INTO tp_TicketTotal (ticketTypeValue,amount) VALUES (?,?)");
      $stmt_insert->bind_param('si',$ticketType[$i],$ticketTypeAmount[$i]);
      $res_exec = $stmt_insert->execute();
      $stmt_insert->close();
    }
  }