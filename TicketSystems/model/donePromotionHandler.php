<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSession();
  $mysqli = dbconnect();

  //ini_set('display_errors',1);

  //受け取り
  $orderID = h($_POST['orderID']);
  $personID = h($_POST['personID']);
  $grade = h($_POST['represent_grade']);
  $part = h($_POST['represent_part']);
  $lname = h($_POST['represent_lname']);
  $fname = h($_POST['represent_fname']);
  $amount_prticket = h($_POST['amount_prticket']); //渉外からチケットをもらった枚数
  $rep_amount_pr = h($_POST['represent_amount_pr']);  //渉外のチケットから売った枚数
  $rep_amount_self = h($_POST['represent_amount_self']);  //自分のチケットから売った枚数
  $accompany = h($_POST['accompany']);
  $num_accompany = 0;
  $timeStamp = date("Y-m-d H:i:s");
  $mem_personID = [];
  if($accompany == 1){
    //同伴者の情報の配列を受け取る
    $mem_grade = $_POST['member_grade'];
    $mem_part = $_POST['member_part'];
    $mem_name = $_POST['member_name'];  //姓名空白区切り
    $mem_amount_pr = $_POST['member_amount_pr'];
    $mem_amount_self = $_POST['member_amount_self'];
    $num_accompany = count($mem_name);
  }

  //同伴者を検索
  for($i=0;$i<$num_accompany;$i++){
    //名前を切り出す
    $name = explode(" ",$mem_name[$i]);
     //学年・パート・名前で検索
    $stmt = $mysqli->prepare("SELECT personID FROM members WHERE grade = ? AND part = ? AND first_name = ? AND last_name = ?"); //名前が一致するIDを検索
    $stmt->bind_param('isss',$mem_grade[$i],$mem_part[$i],$name[1],$name[0]);
    $stmt->execute();
    $stmt->bind_result($mem_pID);
    $result = $stmt->fetch();
    if($result == NULL){
      echo "<!-- fail to select user : $mysqli->error -->";
    }else{
      echo "<!--succeed select of ".$mem_pID."-->";
    }
    $mem_personID[] = $mem_pID;
    $stmt->close();
  }

  //_amount_prの合計値をall_amount_prにセット
  //_amount_prと_amount_selfの全合計値をactualAmountにセット
  $all_amount_pr = 0; //渉外用チケットのみの販売枚数
  $all_amount_pr += $rep_amount_pr;
  for($i=0;$i<$num_accompany;$i++){
    $all_amount_pr += $mem_amount_pr[$i];
  }
  $actualAmount = $all_amount_pr + $rep_amount_self;
  for($i=0;$i<$num_accompany;$i++){
    $actualAmount += $mem_amount_self[$i];
  }

  //promotionの更新
  $stmt = $mysqli->prepare("UPDATE tp_Promotions SET finishFlag = ?,finishTime=?,actualAmount=? WHERE orderID=?");
  $finishFlag = 1;
  $stmt->bind_param('isii',$finishFlag,$timeStamp,$actualAmount,$orderID);
  $result = $stmt->execute();
  if(!$result){
    echo "<!-- fail to update tp_Promotions : $mysqli->error -->";
  }else{
    echo "<!--succeed 1-->";
  }
  $stmt->close();

  //amount_prticket - sum(_amount_pr)の分をfinish_promotionのamountとするorderを発注
  $numReturn = $amount_prticket - $all_amount_pr;
  echo "<!--numReturn=".$numReturn."-->";
  $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,orderTime,finishFlag) VALUES (?,?,?,?,?)");
  $finishFlag = 0;
  $orderType = 7;
  $stmt->bind_param('iiisi',$personID,$orderType,$numReturn,$timeStamp,$finishFlag);
  $result = $stmt->execute();
  if(!$result){
    echo "<!-- fail to insert order of finish_promotion : $mysqli->error -->";
  }else{
    echo "<!--succeed 2-->";
  }
  $stmt->close();

  //人ごとに、_amount_prの分を代表者から受け取り、sold(id=2)で発注し、tp_MemberTicketsの更新
  //同伴者
  for($i=0;$i<$num_accompany;$i++){
    //代表者から受け取る注文
    $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,response,orderTime,finishFlag,finishTime) VALUES (?,?,?,?,?,?,?)");
    $finishFlag = 1;
    $orderType = 8;
    $stmt->bind_param('iiiisis',$mem_personID[$i],$orderType,$mem_amount_pr[$i],$mem_amount_pr[$i],$timeStamp,$finishFlag,$timeStamp);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to insert order of sold : $mysqli->error -->";
    }else{
      echo "<!--succeed 3.1-->";
    }
    $stmt->close();

    //代表者の枚数を減らす注文
    $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,response,orderTime,finishFlag,finishTime) VALUES (?,?,?,?,?,?,?)");
    $finishFlag = 1;
    $orderType = 9;
    $stmt->bind_param('iiiisis',$personID,$orderType,$mem_amount_pr[$i],$mem_amount_pr[$i],$timeStamp,$finishFlag,$timeStamp);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to insert order of sold : $mysqli->error -->";
    }else{
      echo "<!--succeed 3.2-->";
    }
    $stmt->close();

    //soldのorder入力
    $mem_amount_all = $mem_amount_pr[$i] + $mem_amount_self[$i];
    $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,response,orderTime,finishFlag,finishTime) VALUES (?,?,?,?,?,?,?)");
    $finishFlag = 1;
    $orderType = 2;
    $stmt->bind_param('iiiisis',$mem_personID[$i],$orderType,$mem_amount_all,$mem_amount_all,$timeStamp,$finishFlag,$timeStamp);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to insert order of sold : $mysqli->error -->";
    }else{
      echo "<!--succeed 3-->";
    }
    $stmt->close();

    //tp_MemberTicketsの更新
    //selfの枚数を減らし、allの枚数売り上げを増やす
    $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have - ? , sold = sold + ? WHERE personID = ?");
    $stmt->bind_param('iii',$mem_amount_self[$i],$mem_amount_all,$mem_personID[$i]);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to update tp_MemberTickets : $mysqli->error -->";
    }else{
      echo "<!--succeed 4-->";
    }
    $stmt->close();
    //tp_TicketTotalの更新
    //販売済みを増やす
    $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 4");  //団員販売済み
    $stmt->bind_param('i',$mem_amount_all);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
    }
    $stmt->close();
    //団員所持を減らす
    $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 3");  //団員所持
    $stmt->bind_param('i',$mem_amount_all);
    $result = $stmt->execute();
    if(!$result){
      echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
    }
    $stmt->close();
  }
  //代表者のsold
  $rep_amount_all = $rep_amount_pr + $rep_amount_self;
  $stmt = $mysqli->prepare("INSERT INTO tp_Orders (personID,orderTypeID,amount,response,orderTime,finishFlag,finishTime) VALUES (?,?,?,?,?,?,?)");
  $finishFlag = 1;
  $orderType = 2;
  $stmt->bind_param('iiiisis',$personID,$orderType,$rep_amount_all,$rep_amount_all,$timeStamp,$finishFlag,$timeStamp);
  $result = $stmt->execute();
  if(!$result){
    echo "<!-- fail to insert order of sold : $mysqli->error -->";
  }else{
    echo "<!--succeed 5-->";
  }
  $stmt->close();
  
  //代表者のtp_MemberTicketsの更新
  //all_amount_prの分とrep_amount_selfの分を減らす
  //rep_amount_allの分を増やす
  $num_dec = $all_amount_pr + $rep_amount_self;
  $stmt = $mysqli->prepare("UPDATE tp_MemberTickets SET have = have - ? , sold = sold + ? WHERE personID = ?");
  $stmt->bind_param('iii',$num_dec,$rep_amount_all,$personID);
  $result = $stmt->execute();
  if(!$result){
    echo "<!-- fail to update tp_MemberTickets : $mysqli->error -->";
  }else{
    echo "<!--succeed 6-->";
  }
  $stmt->close();  
  //tp_TicketTotalの更新
  //販売済みを増やす
  $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount + ? WHERE ticketTypeCode = 4");  //団員販売済み
  $stmt->bind_param('i',$rep_amount_all);
  $result = $stmt->execute();
  if(!$result){
    echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
  }
  $stmt->close();
  //団員所持を減らす
  $stmt = $mysqli->prepare("UPDATE tp_TicketTotal SET amount = amount - ? WHERE ticketTypeCode = 3");  //団員所持
  $stmt->bind_param('i',$rep_amount_all);
  $result = $stmt->execute();
  if(!$result){
    echo "<!-- fail to update tp_TicketTotal : $mysqli->error -->";
  }
  $stmt->close();

  //receiveFinishPromotionで、渉外が受け取った時に、haveを減らす


  dbclose($mysqli);  
?>