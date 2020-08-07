<?php
  //sum,ticketType[],ticketTypeAmount[]
  /**
   * $prhave = sum - SUM(ticketTypeAmount)が渉外所持枚数
   * insert $prhave
   * for($i=0;$i<size(ticketType);i++)
   * ticketType[i],ticketTypeAmount[i]をinsert
   * 
   */
  //ini_set('display_errors',1);
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSecretSession();
  $mysqli = dbconnect();

  $sum = $_POST["sum"];
  $ticType=$_POST["ticketType"];
  $ticTypeAm = $_POST["ticketTypeAmount"];

  //状態記録用
  $status = "";
  //渉外所持合計数を産出
  $sumpart = 0;
  for($i=0;$i<count($ticTypeAm);$i++){
    $sumpart += $ticTypeAm[$i];
  }
  $prhave = $sum - $sumpart; //手元の渉外チケットの合計数
  //tp_TicketTotalにinsert
  $stmt = $mysqli->prepare("INSERT INTO tp_TicketTotal (ticketTypeValue,amount) VALUES (?,?)");
  $typeValue = "渉外所持";
  $stmt->bind_param('si',$typeValue,$prhave);
  if($stmt->execute()){
    $status .= "SucInsStart";
  }else{
    $status .= "FailInsStart";
  }
  $stmt->close();

  //全てのものを挿入していく
  for($i=0;$i<count($ticTypeAm);$i++){
    $stmt = $mysqli->prepare("INSERT INTO tp_TicketTotal (ticketTypeValue,amount) VALUES (?,?)");
    $stmt->bind_param('si',$ticType[$i],$ticTypeAm[$i]);
    if($stmt->execute()){
      $status .= "-SucIns$i";
    }else{
      $status .= "-FailIns$i";
      echo "<!--$status--$mysqli->error-->";
    }
    $stmt->close();
  }
  echo "<!--$status-->";
  dbclose($mysqli);
?>