<?php
  //ini_set("display_errors",1);
  //error_reporting(E_ALL);
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  accessFilter();
  $mysqli = dbconnect();
  
  //responseテーブルから、responseTimeが本日のものを探し、枚数を調べる
  //tp_OrdersとJOINして、orderTypeごとに集計(finishPromotion,sold_with_reserve,want_retuen)
  $stmt = $mysqli->prepare("SELECT SUM(tp_Responses.amount),orderTypeName FROM tp_Responses INNER JOIN tp_Orders USING(orderID) INNER JOIN tp_OrderTypes USING(orderTypeID) WHERE responseTime BETWEEN ? AND ? AND (orderTypeID = 3 OR orderTypeID = 5 OR orderTypeID = 7) GROUP BY orderTypeName");
  if(!$stmt){
    echo "<!--failPrepare:".$mysqli->error."-->";
  }
  $today = new DateTime();  //今日の0:00:00
  $today->setTime(0,0,0);
  $now = new DateTime();  //現在時刻
  $todayStr = $today->format("Y-m-d H:i:s");
  $nowStr = $now->format("Y-m-d H:i:s");
  $result = $stmt->bind_param('ss',$todayStr,$nowStr);
  if(!$result){
    echo "<!--failBindParam:".$stmt->error."-->";
  }
  $result = $stmt->execute();
  $stmt->bind_result($sum,$orderTypeName);
  if(!$result){
    echo "<!--failExecute:".$mysqli->error."-->";
  }
  $sum_sum = 0;
  $rows_sum = array();
  while($stmt->fetch()){
    $rows_sum[] = array("sum"=>$sum,"orderTypeName"=>$orderTypeName);
    $sum_sum += $sum;
  }
  $stmt->close();
  dbclose($mysqli);

  require_once(ROOT.'/view/header.php');
  getHeader("本日のチケット受け取り枚数確認","private");
?>
<h2>本日のチケット受け取り枚数確認</h2>
<p>日付範囲：<?php echo $today->format("Y-m-d H:i:s")." ~ ".$now->format("Y-m-d H:i:s"); ?></p>
<br>
<div class="table-responsive">
	<table class='table text-nowrap table-striped' id="countTodayTable">
    <tr>
      <th>チケットの種類</th>
      <th>合計枚数</th>
    </tr>
    <?php foreach($rows_sum as $row1): ?>
    <tr>
      <td><?=$row1['orderTypeName']?></td>
      <td><?=$row1['sum']?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
      <td>全合計</td>
      <td><?=$sum_sum?></td>
    </tr>
  </table>
</div>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>