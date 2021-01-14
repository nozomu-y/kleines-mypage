<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);

  //未処理削除
  if(isset($_GET['process'])){
    //処理を行う
    $_SESSION['tp_status'] = "delete-order";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
  }
  
  $pageTitle = "販売状況・履歴確認";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
  require_once 'linkHandler.php';
?>
<p class="tx">改装中</p>
<table class="table-wide">
  <tr class="th">
    <th class="orderID">orderID</th>
    <th class="orderTypeName">オーダー種別</th>
    <th class="amount">枚数</th>
    <th class="response">対応済み枚数</th>
    <th class="orderTime">注文時刻</th>
    <th class="finishFlag">完了フラグ</th>
    <th class="finishTime">完了時刻</th>
    <th class="details">詳細・取消</th>
  </tr>
  <?php
    //Order一覧を検索
    $stmt_history = $mysqli->prepare(
      "SELECT orderID, orderTypeName, amount, response, orderTime, finishFlag, finishTime 
       FROM tp_Orders INNER JOIN tp_OrderTypes USING(orderTypeID) WHERE id = ?");
    $stmt_history->bind_param('i', $USER->id);
    $stmt_history->execute(); //SQLの実行
    $stmt_history->bind_result($orderID, $orderTypeName, $amount, $response, $orderTime, $finishFlag, $finishTime);
    //連想配列で取得
    while($result = $stmt_history->fetch()): ?>
  <tr class="td">
    <td class="orderID"><?=$orderID?></td>
    <td class="orderType"><?=$orderTypeName?></td>
    <td class="amount"><?=$amount?></td>
    <td class="response"><?=$response?></td>
    <td class="orderTime"><?=$orderTime?></td>
    <td class="finishFlag"><?=$finishFlag?></td>
    <td class="finishTime"><?=$finishTime?></td>
    <td class="details"><?php linkHandle($finishFlag, $orderTypeName, $orderID); ?></td>
  </tr>
  <?php endwhile; ?>
</table>
<?php require_once TP_ROOT.'/include/footer.php'; ?>