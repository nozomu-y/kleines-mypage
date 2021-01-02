<?php
  //require_once($_SERVER['DOCUMENT_ROOT']."/TicketSystems/kleines-mypage/Common/init_page.php");
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  $pageTitle = "初期化";
  $applyStyle = "secret";
  require_once(ROOT.'/include/header.php');
?>
<p class="tx">初期化時の挙動</p>
<p class="tx">
  tp_MemberTickets, tp_TicketTotal, tp_Reserves, tp_Responses, tp_Promotions, tp_Orders,
  tp_OrderTypes, tp_Permissions の各テーブルを、存在していなかったら作成し、存在していたら空にする。<br>
  その後、membersから、tp_MemberTicketsに入っていない団員のIDを抽出して挿入する<br>
  その後、全メンバーについて、have,soldの値を0にセット</p><br>
<p class="tx">初期化しますか？</p>
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
        <form action="ticketAmount.php" method="post">
          <input type="hidden" name="process" value="initTables">
          <button class="btn btn-primary" type="submit">初期化する</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- import js files-->
<script src="<?=SERVER?>/include/js/form-modal.js"></script>
<?php require_once(ROOT.'/include/footer.php'); ?>