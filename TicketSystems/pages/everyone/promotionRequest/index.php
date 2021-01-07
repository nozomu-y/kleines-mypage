<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);

  //一度だけ処理を行う
  if(isset($_POST['process']) && strcmp($_POST['process'], "submit")==0){
    require_once __DIR__.'/resolveOrder.php';
    $_SESSION['tp_status'] = "succeed-submit";
    header("Location: ".$_SERVER['PHP_SELF']); //更新対策
    exit();
  }

  $pageTitle = "情宣アポ登録フォーム";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" class="needs-validation" novalidate>
  <br>
  <div class="form-group">
    <p class="tx">訪問団体名</p>
    <input class="form-text js-form-item" type="text" name="groupName" placeholder="団体名を入力してください" required>
    <div class="required-feedback">名前を入力してください</div>
    <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
  </div>
  <div class="form-group">
    <p class="tx">追加で欲しいチケット枚数</p>
    <input type="text" class="form-text js-form-item js-valid-amount" name="amount" id="amount" placeholder="枚数を入力してください" required>
    <p class="tx-sm">個人で持っているチケットで足りる場合は0を入力してください</p>
    <p class="tx-sm">余った分は情宣終了後に回収します</p>
    <div class="required-feedback">枚数を入力してください</div>
    <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
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
  <input type="hidden" name="process" value="submit">
  
  <button class="btn btn-primary js-modal-open js-form-confirm" data-target="confirmModal">入力確認</button>
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
<script src="promotionRequest.js"></script>
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>