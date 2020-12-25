<?php
  require_once(__DIR__.'/initTables.php');
  //require_once($_SERVER['DOCUMENT_ROOT']."/TicketSystems/kleines-mypage/Common/init_page.php");
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  $pageTitle = "チケット枚数設定";
  $applyStyle = "secret";
  require_once(ROOT.'/include/header.php');
?>
<p class="tx"><?=$status?></p>
<p class="tx">チケット枚数設定ページ</p>

<button class="btn btn-primary js-modal-open js-form-confirm" data-target="#confirmModal">はい</button>
<div class="modal js-modal" id="confirmModal">
  <div class="modal-bg js-modal-close"></div>
  <div class="modal-content">
    <div class="modal-header"><div class="modal-title">入力確認</div>
      <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
    </div>
    <div class="modal-main">
      <p class="tx">本当に初期化しますか？</p>
    </div>
    <div class="modal-footer">
      <div class="modal-left">
        <button class="btn btn-secondary js-modal-close">戻る</button>
      </div>
      <div class="modal-right">
        <button class="btn btn-primary">初期化する</button>
      </div>
    </div>
  </div>
</div>
<!-- import js files-->
<script src="<?=SERVER?>/pages/js/form-modal.js"></script>
<?php require_once(ROOT.'/include/footer.php'); ?>