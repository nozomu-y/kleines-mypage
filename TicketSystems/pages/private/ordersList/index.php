<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  require_once __DIR__.'/ticketTypeController.php';

  //一度だけ処理を行う
  if(isset($_POST['process']) && strcmp($_POST['process'], "resolve")==0){
    require_once __DIR__.'/resolveOrder.php';
    $_SESSION['tp_status'] = "succeed-resolve";
    header("Location: ".$_SERVER['PHP_SELF']."?orderTypeID=".$_GET['orderTypeID']); //更新対策
    exit();
  }

  $pageTitle = $pageTitle_;
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx"><?=$message?></p>
<?php 
  //全ての未解決オーダーを取得
  $q_select = 
    "SELECT id, grade, last_name, first_name, part, amount, response, orderID FROM tp_Orders 
    INNER JOIN members USING(id) 
    WHERE finishFlag = 0 AND deleteFlag = 0 AND orderTypeID = $orderTypeID 
    ORDER BY grade ASC, part ASC";
  $mysqli->query($q_select);
  $result = $mysqli->query($q_select);
  $orders = [];
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $orders[] = $row;
  }
  $result->free();
?>
<!-- filters -->
<h2>団員フィルター</h2>
<?php include TP_ROOT."/include/btn-filter/filter.php"; ?>
<h2>オーダーリスト</h2>
<form action="<?=$_SERVER['PHP_SELF']."?orderTypeID=$orderTypeID"?>" method="post">
  <table class="js-filter-table">
    <tr class="th">
      <th class="flag">完了</th>
      <th class="grade">学年</th>
      <th class="part">パート</th>
      <th class="name">名前</th>
      <th class="amount">枚数</th>
      <th class="limited">配布枚数指定</th>
    </tr>
    <?php foreach($orders as $order):?>
    <tr class="td tx">
      <td class="flag">
        <input type="checkbox" class="form-check-input" name="orderID[]" value="<?=$order['orderID']?>">
      </td>
      <td class="grade"><?=$order['grade']?></td>
      <td class="part"><?=$order['part']?></td>
      <td class="name"><?=$order['last_name']." ".$order['first_name']?></td>
      <td class="amount">
        <?php
          $amount = $order['amount'] - $order['response'];
          echo($amount);
        ?>
      </td>
      <td class="limited">
        <select class="select-sm" name="<?="amount".$order['orderID']?>">
          <option value="<?=$amount?>" selected="selected">指定しない</option>
          <?php for($i = 1; $i<$amount; $i++): ?>
          <option value="<?=$amount - $i?>"><?=$amount - $i?></option>
          <?php endfor; ?>
        </select>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <input type="hidden" name="process" value="resolve">
  <button type="submit" class="btn btn-primary">更新</button>
</form>
<?php require_once TP_ROOT.'/include/footer.php'; ?>