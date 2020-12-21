<?php
  //require_once($_SERVER['DOCUMENT_ROOT']."/TicketSystems/kleines-mypage/Common/init_page.php");
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  require_once(__DIR__.'/ticketTypeController.php');
  $pageTitle = $pageTitle_;
  $applyStyle = "everyone";
  require_once(ROOT.'/include/header.php');
?>
<p class="tx">改装中</p>
<p class="tx">指定フォーム：<?=$orderType?></p>
<?php require_once(ROOT.'/include/footer.php'); ?>