<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  accessFilter();
  $mysqli = dbconnect();

  if($_POST['submit']=="submit"){
    //処理呼び出し
    require_once(ROOT.'/model/ticketInspectionHandler.php');
    //SESSION設定
    $_SESSION["tp_status"] = "completeInspection";
    header("Location:{$_SERVER['PHP_SELF']}");
    exit();
  }

  require_once(ROOT.'/view/header.php');
  getHeader("チケット中間点検","private");

  if(isset($_SESSION["tp_status"])&&strcmp($_SESSION['tp_status'],"completeInspection")==0){
    echo "<p>中間点検が完了しました</p>";
    unset($_SESSION["tp_status"]);
  }
?>
<h2>チケット中間点検</h2>
<p>チケットの枚数が表示通りになるように、実際のチケットの枚数を調整してください</p>
<?php
  $sql = "SELECT ticketTypeCode,ticketTypeValue,amount FROM tp_TicketTotal";
  $result = $mysqli->query($sql);
  if($result == NULL){
    echo "<!-- fail select : code : $mysqli->error -->";
    exit();
  }
  //連想配列で取得
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $rows[] = $row;
  }
  //結果セットを解放
  $result->free();
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
  <div class="table-responsive">
    <table class='table text-nowrap table-striped'>
      <tr>
        <th class="flag">確認</th>
        <th class="ticketTypeValue">チケット種別</th>
        <th class="amount">枚数</th>
      </tr>
      <?php foreach($rows as $row): ?>
      <tr>
        <td class="flag">
          <?php if($row['ticketTypeCode']!=3 && $row['ticketTypeCode']!=4): ?>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="ticketTypeCode[]" value="<?php echo $row['ticketTypeCode']; ?>" required>
            <?php //メモ：チェックされてたら修正不可にして、取り消し線を表示し、色を灰色にする(javaScript?) ?>
          </div>
          <?php endif; ?>
        </td>
        <td class="ticketTypeValue"><?php echo $row['ticketTypeValue']; ?></td>
        <td class="amount"><?php echo $row['amount']; ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <input type="hidden" name="submit" value="submit">
  <button type="submit" class="btn btn-primary">点検完了</button>
</form>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>