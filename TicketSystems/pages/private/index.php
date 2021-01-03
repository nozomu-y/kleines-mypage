<?php
  require_once __DIR__.'/../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  $pageTitle = "渉外用TOP";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">top page test</p>
<h2>Sub Title</h2>
<p class="tx">sub title</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>