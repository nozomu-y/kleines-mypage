<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  $pageTitle = "本日回収チケット枚数確認";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';

  $today = new DateTime();  //今日の0:00:00
  $today->setTime(0,0,0);
  $now = new DateTime();  //現在時刻
  $todayStr = $today->format("Y-m-d H:i:s");
  $nowStr = $now->format("Y-m-d H:i:s");
  $stmt_select = $mysqli->prepare(
    "SELECT SUM(tp_Responses.amount) AS sum, orderTypeName FROM tp_Responses 
    INNER JOIN tp_Orders USING(orderID) 
    INNER JOIN tp_OrderTypes USING(orderTypeID)
    WHERE responseTime BETWEEN ? AND ? AND (orderTypeID = 3 OR orderTypeID = 5 OR orderTypeID = 7) 
    GROUP BY orderTypeName");
  $stmt_select->bind_param('ss',$todayStr,$nowStr);
  $stmt_select->execute();
  $stmt_select->bind_result($sum_amount, $orderTypeName); 
  $sum_all = 0; //全体の合計枚数
  $sum_orderType = array(); //orderTypeごとの合計枚数
  while($stmt_select->fetch()){
    $sum_orderType[] = array("sum"=>$sum_amount, "orderTypeName"=>$orderTypeName);
    $sum_all += $sum_amount;
  }
  $stmt_select->close();
?>
<p class="tx"><?=$todayStr?> ~ <?=$nowStr?><br>に回収したチケットの一覧です</p>
<table class='table text-nowrap table-striped' id="countTodayTable">
  <tr class="th">
    <th>チケットの種類</th>
    <th>合計枚数</th>
  </tr>
  <?php foreach($sum_orderType as $sum_type): ?>
  <tr class="td">
    <td><?=$sum_type['orderTypeName']?></td>
    <td><?=$sum_type['sum']?></td>
  </tr>
  <?php endforeach; ?>
  <tr class="td">
    <td>全合計</td>
    <td><?=$sum_all?></td>
  </tr>
</table>

<?php require_once TP_ROOT.'/include/footer.php'; ?>