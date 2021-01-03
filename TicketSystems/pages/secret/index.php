<?php
  require_once __DIR__.'/../../include/tp_init.php';
  secretFilter(MAX_CHIEF_PERM, $mysqli);
  $pageTitle = "渉外チーフ用TOP";
  $applyStyle = "secret";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">top page test</p>
<h2>Sub Title</h2>
<p class="tx">sub title</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>