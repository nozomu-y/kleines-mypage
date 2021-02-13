<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  secretFilter(MAX_CHIEF_PERM, $USER->id, $mysqli);
  $pageTitle = "初期化";
  $applyStyle = "secret";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">初期化時の挙動</p>
<p class="tx">①必要なテーブルを作成する。存在していた時は空にする。</p>
<p class="tx-sm">必要なテーブル一覧：tp_MemberTickets, tp_TicketTotal, tp_Reserves, tp_Responses, tp_Promotions, tp_Orders,
  tp_OrderTypes, tp_Permissions</p><br>
<p class="tx">②必要なタプルを挿入する</p>
<p class="tx-sm">
  必要なタプル：<br>
  　tp_MemberTickets, tp_Permissionsに全団員分のタプル、<br>
  　tp_TicketTotalに必要な4つのチケット種別、<br>
  　tp_OrderTypesにオーダー種別を挿入
</p><br>
<p class="tx">初期化しますか？</p>
<button class="btn btn-primary js-modal-open js-form-confirm" data-target="confirmModal">はい</button>
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
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>