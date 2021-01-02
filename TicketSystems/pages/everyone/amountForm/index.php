<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  require_once __DIR__.'/ticketTypeController.php';
  $pageTitle = $pageTitle_;
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">
  改装中<br>
  指定フォーム：<?=$orderType?>
</p>
<p class="tx"><?=$message?></p>
<form method="post" action="#" class="needs-validation" novalidate>
  <div class="form-group">
    <input type="text" class="form-text js-form-item js-valid-amount" name="amount" id="amount" placeholder="枚数を入力してください" required>
    <div class="required-feedback">枚数を入力してください</div>
    <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
    <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
  </div>
  <div class="form-group">
    <?php /*<input type="hidden" name="id" value="<?=h($_SESSION['mypage_id'])?>" */ ?>
    <?php /*<input type="hidden" name="name" value="<?=h(USER->name)?>"> */?>
    <input type="hidden" class="js-form-item" name="orderTypeID" value="<?=$orderTypeID?>">
    <input type="hidden" class="js-form-item" name="orderType" value="<?=$orderType?>">
  </div>
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
<!-- import js files-->
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>