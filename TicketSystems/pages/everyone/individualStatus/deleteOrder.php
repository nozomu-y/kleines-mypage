<?php 
  /**
   * 入力：orderID、orderTypeName?
   * request, want_returnの場合：
   *  delFlag, delTimeをセットして終了
   * want_promoの場合(中止ボタンが押された場合)
   *  ・response > 0なら、responseの枚数分の返却命令をordersに出す(orderType作成)
   *  ・orderでdelFlag, delTimeのセット(delFlag==1の場合、情宣リストに"中止"と掲載)
   * sold_with_reserveの場合(預かりで削除が押された時)
   *  ・response > 0なら、返却オーダー
   *  ・reserveの反映(りすとから削除)
   *  ・orderでdelFlag, delTimeのセット
   *  ・MemberTickets, TicketTotalの更新
   */
  require_once TP_ROOT."/include/orders/orderDeleteHandler.php";
  $orderID = htmlspecialchars($_POST['orderID']);
  $orderTypeID = htmlspecialchars($_POST['orderTypeID']);
  deleteOrder($USER->id, $orderID, $orderTypeID, $mysqli);
  if($orderTypeID == 5){  //sold_with_reserve
    deleteReserve($orderID, $mysqli);
  }
  

