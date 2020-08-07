<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSession();
  $mysqli = dbconnect();

  /**
   * 人を特定する(今までのやつのコピペ)
   * orderTableにsoldを挿入(finishFlag=1)
   * reserve_use==1なら、
   * ・グループ数を取得(配列にcount)
   * ・グループ数だけループし(i<numGroup),orderに追加(orderType=???)& reserveに追加
   */

  //入力の受け取り
  $personID = h($_POST['personID']);
  $amount_all = h($_POST['amount_all']);
  $reserve_use = h($_POST['reserve_use']);
  $amount_reserve = 0;  //預かり利用枚数
  $timeStamp = date("Y-m-d H:i:s");
  if($reserve_use == 1){
    //預かり情報の配列を受け取る
    $guest_lname = $_POST['guest_lname'];
    $guest_fname = $_POST['guest_fname'];
    $guest_lname_kana = $_POST['guest_lname_kana'];
    $guest_fname_kana = $_POST['guest_fname_kana'];
    $guest_amount = $_POST['guest_amount'];
    $price = $_POST['price'];
    //預かり利用枚数を求める
    for($i=0;$i<count($guest_amount);$i++){
      $amount_reserve += $guest_amount[$i];
    }
  }
  $amount = $amount_all - $amount_reserve;  //預かりを利用しないsoldの枚数

  //soldのみをtp_Orders Tableに挿入
  if($amount>0){
    $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,response,orderTime,finishFlag,finishTime) VALUES (?,?,?,?,?,?,?)");
    $finishFlag = 1;
    $orderType = 2;
    $stmt->bind_param('iiiisis',$personID,$orderType,$amount,$amount,$timeStamp,$finishFlag,$timeStamp);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to insert tp_Orders in all : $mysqli->error -->";
    }
    $stmt->close();

    //tp_MemberTickets tableにsoldのみの分を挿入
    $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have - ? , sold = sold + ? WHERE personID = ?");
    $stmt->bind_param('iii',$amount,$amount,$personID);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- failUpdMemTic : $mysqli->error -->";
    }
    $stmt->close();
    //tp_TicketTotalの団員所持の枚数を減らし、販売済みを増やす
    //販売済みを増やす
    $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 4");  //団員販売済み
    $stmt->bind_param('i',$amount);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
    }
    $stmt->close();
    //団員所持を減らす
    $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 3");  //団員所持
    $stmt->bind_param('i',$amount);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
    }
    $stmt->close();

  }else if($amount<0){
    echo "<!--sold amount is minus-->";
  }

  //預かり利用するなら、グループの数だけ繰り返しで処理
  if($reserve_use==1){
    $numGroup = count($guest_lname);
    echo "<!-- numGroup:$numGroup -->";
    for($i=0;$i<count($guest_lname);$i++){
      //tp_Ordersへの登録
      $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,orderTime,finishFlag) VALUES (?,?,?,?,?)");
      $finishFlag = 0;
      $orderType = 5; //sold_with_reserve
      $stmt->bind_param('iiisi',$personID,$orderType,$guest_amount[$i],$timeStamp,$finishFlag);
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to insert tp_Orders in guest : $mysqli->error -->";
      }
      $stmt->close();
      //reserveへの登録
      $orderID = $mysqli->insert_id;  //orderIDを取得
      echo "<!-- insertID:$orderID -->";
      $stmt = $mysqli->prepare("INSERT INTO tp_Reserves (orderID,lastName,firstName,lastNameKana,firstNameKana,price,visitFlag) VALUES (?,?,?,?,?,?,?)");
      $visitFlag = 0;
      $stmt->bind_param('issssii',$orderID,$guest_lname[$i],$guest_fname[$i],$guest_lname_kana[$i],$guest_fname_kana[$i],$price[$i],$visitFlag);
      echo "<!-- ".$orderID." ".$guest_lname[$i]." ".$guest_fname[$i]." ".$guest_lname_kana[$i]." ".$guest_fname_kana[$i]." ".$price[$i]." ".$visitFlag." -->";
      //amount,personIDはorderIDから取得する
      $result = $stmt->execute();
      if(!$result){
        echo "<!-- fail to insert tp_Reserves in guest : $mysqli->error -->";
      }
      $stmt->close();
      //memo soldWithReserveのtp_OrdersのfinishFlagが1になったときに枚数変更(have->soldへの振替)を実施
    }
  }

  dbclose($mysqli);
?>