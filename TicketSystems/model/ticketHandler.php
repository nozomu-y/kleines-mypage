<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSession();
  $mysqli = dbconnect();

  //内容チェック
  if(isset($_POST['orderID']) && is_array($_POST['orderID'])){
    $IDs = $_POST['orderID'];
    $status = "<!-- orderIDisExist:".count($IDs)."-->";
    if(count($IDs)>0){
      $status.= "<!--IDs[0]=".$IDs[0]."-->";
    }
  }else{
    $status.= "orderIDisNotExist";
  }
  $response_personID = h($_SESSION['mypage_personID']);
  //手続きごとにSQLを処理していく
  foreach($IDs as $reqID){
    //対応する手続きのpersonIDとamountを取得
    $status .= "<!-- reqID is $reqID -->";
    $stmt = $mysqli->prepare("SELECT personID,amount,response FROM tp_Orders WHERE orderID = ?");
    $stmt->bind_param('i',$reqID);
    $stmt->execute();
    $stmt->bind_result($per,$amo,$rsp);
    $result = $stmt->fetch();
    if($result == NULL){
      //エラー処理
      $status .= "<!--failToSelectByOrderID-->";
    }else{
      $status .= "<!-- succeed select by orderID -->";
    }
    $personID = $per;
    $a = $amo - $rsp; //達成するために必要な残り枚数
    if($stmt->fetch()!=NULL){
      $status .= "<!-- orderID is not unique -->";
    }
    $status .= "<!-- finish get amount & reqID -->";
    $stmt->close();

    //枚数が手続き通りかを調べ、$amountに対応する値をセット
    if($a == $_POST['amount'.$reqID]){
      $amount = $a; //$amount : 手続きした量
      $status .= "<!-- amount is full -->";
      $full = true;
    }else{
      $amount = $_POST['amount'.$reqID];  //手続きした量
      $delta = $a - $_POST['amount'.$reqID];  //手続き完了のために足りなかった量
      $status .= "<!-- amount is not full -->";
      $full = false;
    }
    //orderTypeごとのSQL
    switch($_POST['orderType']){
      case 1: //request
        $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have + ? WHERE personID = ?");
        $stmt->bind_param('ii',$amount,$personID);
        $res_mt = $stmt->execute();
        $stmt->close();
        //tp_TicketTotal テーブルをUPDATE
        //渉外所持を減らす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 1");  //渉外所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        //団員所持を増やす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 3");  //団員所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        break;
      case 3: //want_return
        $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have - ? WHERE personID = ?");
        $stmt->bind_param('ii',$amount,$personID);
        $res_mt = $stmt->execute();
        $stmt->close();
        //tp_TicketTotal テーブルをUPDATE
        //渉外所持を増やす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 1");  //渉外所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        //団員所持を減らす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 3");  //団員所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        break;
      case 4: //want_promotion
        $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have + ? WHERE personID = ?");
        $stmt->bind_param('ii',$amount,$personID);
        $res_mt = $stmt->execute();
        $stmt->close();
        //tp_TicketTotal テーブルをUPDATE
        //渉外所持を減らす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 1");  //渉外所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        //団員所持を増やす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 3");  //団員所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        break;
      case 5: //sold_with_reserve
        $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET sold = sold + ? , have = have - ? WHERE personID = ?");
        $stmt->bind_param('iii',$amount,$amount,$personID);
        $res_mt = $stmt->execute();
        $stmt->close();
        //tp_TicketTotal テーブルをUPDATE
        //団員所持を減らす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 3");  //団員所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        //預かり済みを増やす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 2");  //預かり用に回収済み
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        break;
      case 7: //finish_promotion
        $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have - ? WHERE personID = ?");
        $stmt->bind_param('ii',$amount,$personID);
        $res_mt = $stmt->execute();
        $stmt->close();
        //tp_TicketTotal テーブルをUPDATE
        //団員所持を減らす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 3");  //渉外所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        //渉外所持を増やす
        $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 1");  //団員所持
        $stmt->bind_param('i',$amount);
        $result = $stmt->execute();
        $stmt->close();
        break;
      default:
        //エラー対策
        break;
    }
    if($res_mt){
      $status .= "<!-- succeed update tp_MemberTickets -->";
      $timeStamp = date("Y-m-d H:i:s");
      //ここで、枚数指定の有無で分岐
      if($full){
        $stmt = $mysqli->prepare("UPDATE tp_Orders SET response = response + ?, finishFlag = 1,finishTime = ? WHERE orderID = ?");
        $stmt->bind_param('isi',$amount,$timeStamp,$reqID);
      }else{
        $stmt = $mysqli->prepare("UPDATE tp_Orders SET response = response + ? WHERE orderID = ?");
        $stmt->bind_param('ii',$amount,$reqID);
      }
      $res_req = $stmt->execute();
      $stmt->close();
      if(!$res_req){
        $status .= "<!-- fail update finishFlag-->";
      }else{
        
      }
    }else{
      $status .= "<!-- fail update tp_MemberTickets -->";
    }
    //responseを作成
    $stmt = $mysqli->prepare("INSERT INTO tp_Responses (orderID,personID,amount,responseTime) VALUES (?,?,?,?)");
    $stmt->bind_param('iiis',$reqID,$response_personID,$amount,$timeStamp);
    $res = $stmt->execute();
    $stmt->close();
    if($res){
      $status .= "<!--succeed insert tp_Responses-->";
    }else{
      $status .= "<!--fail insert tp_Responses-->";
    }

    //echo $status;
  }
?>