<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);

  //未処理削除
  if(isset($_POST['process']) && strcmp($_POST['process'],"del")==0){
    //処理を行う
    require_once __DIR__."/deleteOrder.php";
    $_SESSION['tp_status'] = "delete-order";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
  }
  
  $pageTitle = "販売状況・履歴確認";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
  require_once 'linkHandler.php';
?>
<table class="table-wide">
  <tr class="th">
    <th class="orderID">orderID</th>
    <td class="orderTypeID" style="display:none;">オーダー種別ID</td>
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
    $finishFlag = 0;
    $stmt_history = $mysqli->prepare(
      "SELECT orderID, orderTypeID, orderTypeName, amount, response, orderTime, finishFlag, finishTime 
       FROM tp_Orders INNER JOIN tp_OrderTypes USING(orderTypeID) WHERE id = ? AND deleteFlag = ?");
    $stmt_history->bind_param('ii', $USER->id, $finishFlag);
    $stmt_history->execute(); //SQLの実行
    $stmt_history->bind_result($orderID, $orderTypeID, $orderTypeName, $amount, $response, $orderTime, $finishFlag, $finishTime);
    //連想配列で取得
    while($result = $stmt_history->fetch()): ?>
  <tr class="td">
    <td class="orderID js-modal-item"><?=$orderID?></td>
    <td class="orderTypeID js-modal-item" style="display:none;"><?=$orderTypeID?></td>
    <td class="orderTypeName js-modal-item"><?=$orderTypeName?></td>
    <td class="amount js-modal-item"><?=$amount?></td>
    <td class="response js-modal-item"><?=$response?></td>
    <td class="orderTime"><?=$orderTime?></td>
    <td class="finishFlag"><?=$finishFlag?></td>
    <td class="finishTime"><?=$finishTime?></td>
    <td class="details"><?php linkHandle($finishFlag, $orderTypeName, $orderID); ?></td>
  </tr>
  <?php endwhile; ?>
</table>
<div class="modal js-modal" id="confirmModal">
  <div class="modal-bg js-modal-close"></div>
  <div class="modal-content">
    <div class="modal-header"><div class="modal-title">確認</div>
      <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
    </div>
    <div class="modal-main">
      <p class="tx">この注文を削除してもよろしいですか？</p>
      <div class="js-item-list"></div>
    </div>
    <div class="modal-footer">
      <div class="modal-left">
        <button class="btn btn-secondary js-modal-close">戻る</button>
      </div>
      <div class="modal-right">
        <form method="post" action="<?=$_SERVER['PHP_SELF']?>?process=delete">
          <input type="hidden" name="process" value="del">
          <input type="hidden" name="dummy" value="0">
          <input type="hidden" name="orderTypeID" value="0">
          <input type="hidden" name="orderID" value="0">
          <button type="submit" class="btn btn-danger">削除する</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="list.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>