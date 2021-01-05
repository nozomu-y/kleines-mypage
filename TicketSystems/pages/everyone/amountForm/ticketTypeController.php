<?php
  $orderTypeID = $_GET['orderTypeID'];
  switch($orderTypeID){
    case 1:
      $orderType = 'request';
      $pageTitle_ = 'チケット希望フォーム';
      $message = '欲しい枚数を入力してください';
      break;
    case 3:
      $orderType = 'want_return';
      $pageTitle_ = "チケット返却希望フォーム";
      $message = '返却する枚数を入力してください';
      break;
    case 6:
      $orderType = 'cancel';
      $pageTitle_ = "チケットキャンセル報告";
      $message = "返品された枚数を入力してください";
      break;
    default:
      //不正な操作が行われた時、TOPに飛ばす
      $_SESSION['tp_status'] = "invalid-page";
      header("Location: ../index.php");
      exit();
  }
