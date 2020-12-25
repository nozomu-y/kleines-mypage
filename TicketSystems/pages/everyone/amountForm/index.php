<?php
  //require_once($_SERVER['DOCUMENT_ROOT']."/TicketSystems/kleines-mypage/Common/init_page.php");
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  require_once(__DIR__.'/ticketTypeController.php');
  $pageTitle = $pageTitle_;
  $applyStyle = "everyone";
  require_once(ROOT.'/include/header.php');
?>
<p class="tx">
  改装中<br>
  指定フォーム：<?=$orderType?>
</p>
<p class="tx"><?=$message?></p>
<form method="post" action="#" class="needs-validation" novalidate>
  <div class="form-group">
    <input type="text" class="form-text js-form-item" name="amount" id="amount" placeholder="枚数を入力してください" required>
    <div class="required-feedback">枚数を入力してください</div>
    <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
    <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
  </div>
  <div class="form-group">
    <?php /*<input type="hidden" name="personID" value="<?=h($_SESSION['mypage_personID'])?>" */ ?>
    <?php /*<input type="hidden" name="name" value="<?=h(USER->name)?>"> */?>
    <input type="hidden" class="js-form-item" name="orderTypeID" value="<?=$orderTypeID?>">
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
        <?php //<div class="js-form-value personID">ID <span>XXX</span></div> // ?>
        <?php //<div class="js-form-value name">名前 <span>XXX</span></div> // ?>
        <div class="js-form-value amount">枚数 <span>XXX</span></div>
        <div>オーダー種別：<?=$orderType?></div>
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
<script src="<?=SERVER?>/pages/js/form-modal.js"></script>
<?php require_once(ROOT.'/include/footer.php'); ?>