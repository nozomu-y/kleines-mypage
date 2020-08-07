<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSession();
  $mysqli = dbconnect();

  $id = $_POST['personID'];
  $amount = $_POST['amount'];
  $orderType = $_POST['orderType'];
  $timeStamp = date("Y-m-d H:i:s");


  //tp_Orders Tableに挿入
  //このセクションも全てのorderで共通
  switch($orderType){
    case 1: //request
    case 3: //want_return
    case 4: //want_promotion
      $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,orderTime,finishFlag) VALUES (?,?,?,?,?)");
      $finishFlag = 0;
      $stmt->bind_param('iiisi',$id,$orderType,$amount,$timeStamp,$finishFlag);
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to insert tp_Orders : $mysqli->error -->";
      }else{
        echo "<!-- succeed to insert tp_Orders -->";
      }
      $stmt->close();
      break;
    case 6: //cancel
      $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,response,orderTime,finishFlag,finishTime) VALUES (?,?,?,?,?,?,?)");
      $finishFlag = 1;
      $stmt->bind_param('iiiisis',$id,$orderType,$amount,$amount,$timeStamp,$finishFlag,$timeStamp);
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to insert tp_Orders : $mysqli->error -->";
      }
      $stmt->close();
      break;
    default:
      echo "<!-- invalid orderType -->";
      break;
  }
  
  //他のtableに挿入
  switch($orderType){
    case 1: //request
    case 3: //want_return
      break;
    case 4: //want_promotion : tp_Promotions tableに挿入
      $orderID = $mysqli->insert_id;  //orderIDを取得
      echo "<!--orderID=$orderID-->";
      $groupName = $_POST['groupName'];
      if($_POST['date_determined']==1){
        echo "<!-- date is determined -->";
        $year = $_POST['year'];
        $month = $_POST['month'];
        $day = $_POST['day'];
        $targetDay = $year."-".$month."-".$day;
        $stmt = $mysqli->prepare("INSERT INTO tp_Promotions (orderID,groupName,date) VALUES (?,?,?)");
        $stmt->bind_param('iss',$orderID,$groupName,date("Y-m-d",strtotime($targetDay)));
      }else{
        echo "<!-- date is not determined -->";
        $stmt = $mysqli->prepare("INSERT INTO tp_Promotions (orderID,groupName) VALUES (?,?)");
        $stmt->bind_param('is',$orderID,$groupName);
      }
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to insert tp_Promotions : $mysqli->error -->";
      }else{
        echo "<!-- succeed to insert tp_Promotions -->";
      }
      $stmt->close();
      break;
    case 6: //cancel
      //tp_MemberTickets tableに挿入
      $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have + ? , sold = sold - ? WHERE personID = ?");
      $stmt->bind_param('iii',$amount,$amount,$id);
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to update tp_MemberTickets : $mysqli->error -->";
      }
      $stmt->close();
      //tp_TicketTotal テーブルをUPDATE
      //販売済みを減らす
      $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 4");  //団員販売済み
      $stmt->bind_param('i',$amount);
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to update total_ticket : $mysqli->error -->";
      }
      $stmt->close();
      //団員所持を増やす
      $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 3");  //団員所持
      $stmt->bind_param('i',$amount);
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
      }
      $stmt->close();
      break;
    default:
      echo "<!-- invalid orderType -->";
      break;
  }
  
  dbclose($mysqli);
?>