<?php
  require_once __DIR__.'/../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  $pageTitle = "渉外用TOP";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<h2>開発用メモ</h2>
<p class="tx">・cancel_reserveとcancel_promotionに対応できるようにする</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>