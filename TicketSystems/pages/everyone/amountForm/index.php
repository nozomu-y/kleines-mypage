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
    <input type="text" class="form-text" name="amount" id="amount" placeholder="枚数を入力してください" required>
    <div class="invalid-feedback"><?php //枚数を入力してください ?></div>
  </div>
  <div class="form-group">
    <?php /*<input type="hidden" name="personID" value="<?=h($_SESSION['mypage_personID'])?>" */ ?>
    <input type="hidden" name="orderTypeID" value="<?=$orderTypeID?>">
  </div>
  <button class="btn btn-primary js-modal-open" data-target="confirmModal">入力確認</button>
  <div class="modal js-modal" id="confirmModal">
    <div class="modal-bg js-modal-close"></div>
    <div class="modal-content">
      <div class="modal-header"><div class="modal-title">入力確認</div>
        <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
      </div>
      <div class="modal-main">
        <p class="tx">
          この内容で送信してもよろしいですか？<br>
          名前：???<br>
          枚数：???<br>
          オーダー種別：<?=$orderType?>
        </p>
      </div>
      <div class="modal-footer">
        <div class="modal-left">
          <button class="btn btn-secondary">戻る</button>
        </div>
        <div class="modal-right">
          <button class="btn btn-primary">送信</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- validationのjsをおく-->

<script src="<?=SERVER?>/pages/js/modal.js"></script>
<?php require_once(ROOT.'/include/footer.php'); ?>