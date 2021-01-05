<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);
  $pageTitle = "販売済座席一覧";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">改装中</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>