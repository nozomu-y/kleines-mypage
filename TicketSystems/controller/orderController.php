<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  function assignOrder($orderType){
    switch($orderType){ //orderTypeの数値と意味の対応はDBを確認
      case 1:
      case 3:
      case 4:
      case 6:
        require_once(ROOT.'/model/ticketOrderHandler.php');
        break;
      case 2:
        require_once(ROOT.'/model/ticketSoldHandler.php');
        break;
      case 7:
        echo "<!-- orderTypeID:$orderType -->";
        require_once(ROOT.'/model/donePromotionHandler.php');
        break;
      default:
        //echo "<!-- orderTypeID:$orderType -->";
        exit;
        header("Location: ".ROOT."/view/failureOrder.php?&failure=invalidOrderType");  //エラー画面表示のページにリダイレクト
        exit;
        break;
    }
  }
?>

