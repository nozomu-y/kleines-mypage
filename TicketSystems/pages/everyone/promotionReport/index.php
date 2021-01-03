<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  secretFilter(NO_PERM_NUM, $USER->id, $mysqli);
  $pageTitle = "情宣完了報告";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">改装中</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>