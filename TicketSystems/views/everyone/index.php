<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  $pageTitle = "団員用TOP";
  $applyStyle = "everyone";
  require_once(ROOT.'/include/header.php');
?>
<div id="main">
  <div class="container">
    <h1>Top Page</h1>
    <p class="tx">top page test</p>
    <h2>Sub Title</h2>
    <p class="tx">sub title</p>
  </div>
</div>
<?php require_once(ROOT.'/include/footer.php'); ?>