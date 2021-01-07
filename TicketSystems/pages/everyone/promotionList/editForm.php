<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);

  //編集を実行し、リストに戻る操作
  if(isset($_POST['process']) && strcmp($_POST['process'], "update")==0){
    require_once __DIR__.'/edit.php';
    $_SESSION['tp_status'] = "succeed-update-promotion";
    header("Location: list.php");
    exit();
  }

  //不正な操作だった時の関数
  function invalid(){
    $_SESSION['tp_status'] = "invalid-page";
    header("Location: list.php");
    exit();
  }
  //orderIDを取得する
  if(!isset($_GET['orderID'])){ //orderIDが入力されていなかった場合
    invalid();
  }
  $orderID = htmlspecialchars($_GET['orderID']);
  if(!is_numeric($orderID)){  //数字以外のorderIDが入力されていた場合
    invalid();
  }

  //入力されたorderIDからpromotionの内容を取得
  $stmt_promotion = $mysqli->prepare(
    "SELECT id, groupName, date FROM tp_Promotions 
    INNER JOIN tp_Orders USING(orderID) 
    WHERE orderID = ? AND tp_Promotions.finishFlag = 0");
  $stmt_promotion->bind_param('i', $orderID);
  $stmt_promotion->execute();
  $stmt_promotion->bind_result($personID, $groupName, $date);
  $result = $stmt_promotion->fetch();
  if($result==null || $result == false){
    invalid();
  }
  $stmt_promotion->close();

  //orderと違う人だった場合、listに飛ばす
  if($personID != $USER->id){
    invalid();
  }

  $pageTitle = "情宣詳細編集";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>?orderID=<?=$orderID?>" class="needs-validation" novalidate>
  <br>
  <div class="form-group">
    <p class="tx">訪問団体名</p>
    <input class="form-text js-form-item" type="text" name="groupName" value="<?=$groupName?>" placeholder="団体名を入力してください" required>
    <div class="required-feedback">名前を入力してください</div>
    <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
  </div>
  <div class="form-group">
		<p class="tx">日時はすでに決まっていますか？</p>
    <input type="checkbox" name="date-determined" id="date-determined" value="はい">
    <label class="tx" for="date-determined">はい</label>
  </div>
  <div class="form-group" id="date-select">
    <input class="js-form-item" type="date" name="date" disabled>
    <?php //TODO: datepickerを調べる。IEとかにも対応 ?>
  </div>
  <input type="hidden" name="process" value="update">
  <button class="btn btn-primary js-modal-open js-form-confirm" data-target="confirmModal">入力確認</button>

  <button class="btn btn-secondary">情宣一覧に戻る</button>
  <div class="modal js-modal" id="confirmModal">
    <div class="modal-bg js-modal-close"></div>
    <div class="modal-content">
      <div class="modal-header"><div class="modal-title">入力確認</div>
        <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
      </div>
      <div class="modal-main">
        <p class="tx">この内容で送信してもよろしいですか？</p>
        <div class="js-item-list"></div>
      </div>
      <div class="modal-footer">
        <div class="modal-left">
          <button class="btn btn-secondary js-modal-close">戻る</button>
        </div>
        <div class="modal-right">
          <button class="btn btn-primary">送信</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- import js files -->
<script src="../promotionRequest/promotionRequest.js"></script>
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>