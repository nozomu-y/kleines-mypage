<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);

  //未処理削除
  if(isset($_POST['process']) && $_POST['process'] === "del"){
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
<h2>チケット状況</h2>
<p class="tx"><?=$USER->get_name()?> のチケット状況</p>
<table id="summary">
  <tr class="th">
    <th class="have">所持</th>
    <th class="sold">販売</th>
  </tr>
  <?php
    $stmt_ticket = $mysqli->prepare("SELECT have, sold FROM tp_MemberTickets WHERE id = ?");
    $stmt_ticket->bind_param('i', $USER->id);
    $stmt_ticket->execute();
    $stmt_ticket->bind_result($have, $sold);
    if(!$stmt_ticket->fetch()){
      echo($mysqli->error);
    }
    $stmt_ticket->close();
  ?>
  <tr class="td">
    <td class="have"><?=$have?></td>
    <td class="sold"><?=$sold?></td>
  </tr>
</table>
<h2>履歴・預かり編集・取り消し</h2>
<p class="tx">横に長いので、横スクロールしてください。</p>
<p class="tx">チケット預かりの内容を変更・削除するとき<br>　→「預かり編集」ボタン</p>
<p class="tx">オーダーを取り消す時<br>　→「取消」ボタン(未完了時のみ)</p>
<table id="histroy" class="table-wide">
  <tr class="th">
    <th class="orderID">orderID</th>
    <td class="orderTypeID" style="display:none;">オーダー種別ID</td>
    <th class="orderTypeName">オーダー種別</th>
    <th class="amount">枚数</th>
    <th class="response">対応済み枚数</th>
    <th class="orderTime">注文時刻</th>
    <th class="status">渉外対応</th>
    <th class="finishTime">完了or取消時刻</th>
    <th class="details">詳細・取消</th>
  </tr>
  <?php
    //Order一覧を検索
    $stmt_history = $mysqli->prepare(
      "SELECT orderID, orderTypeID, orderTypeName, amount, response, orderTime, finishFlag, finishTime,
       deleteFlag, deleteTime FROM tp_Orders INNER JOIN tp_OrderTypes USING(orderTypeID) WHERE id = ?");
    $stmt_history->bind_param('i', $USER->id);
    $stmt_history->execute(); //SQLの実行
    $stmt_history->bind_result(
      $orderID, $orderTypeID, $orderTypeName, $amount, $response, 
      $orderTime, $finishFlag, $finishTime, $deleteFlag, $deleteTime);
    //連想配列で取得
    while($result = $stmt_history->fetch()): ?>
  <tr class="td">
    <td class="orderID js-modal-item"><?=$orderID?></td>
    <td class="orderTypeID js-modal-item" style="display:none;"><?=$orderTypeID?></td>
    <td class="orderTypeName js-modal-item"><?=$orderTypeName?></td>
    <td class="amount js-modal-item"><?=$amount?></td>
    <td class="response js-modal-item"><?=$response?></td>
    <td class="orderTime tx-sm"><?=$orderTime?></td>
    <td class="status">
      <?php if($deleteFlag == 1){
        echo("取消");
      }else if($finishFlag == 1){
        echo("済");
      }else{
        echo("未");
      } ?>
    </td>
    <td class="finishTime tx-sm">
      <?php if($deleteFlag == 1){
        echo($deleteTime);
      }else{
        echo($finishTime);
      } ?>
    </td>
    <td class="details"><?php linkHandle($finishFlag, $deleteFlag, $orderTypeName, $orderID); ?></td>
  </tr>
  <?php
    endwhile; 
    $stmt_history->close();
  ?>
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