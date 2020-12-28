<?php
  //一度だけ処理を行う
  //TODO ブラウザバックの対応
  if($_POST['process']=="ticket"){
    require_once(__DIR__.'/configureTickets.php');
    header("Location:".$_SERVER['PHP_SELF']."?submit=done");
    exit();
  }

  //require_once($_SERVER['DOCUMENT_ROOT']."/TicketSystems/kleines-mypage/Common/init_page.php");
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  $pageTitle = "権限設定";
  $applyStyle = "secret";
  require_once(ROOT.'/include/header.php');
?>

<?php require_once(ROOT.'/include/footer.php'); ?>