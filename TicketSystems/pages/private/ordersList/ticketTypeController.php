<?php
  //GETメソッドでIDを取得
  //許可されたIDの場合、それ用の操作を行う
  //IDごとにpageType用の変数を振り分ける
  $orderTypeID = $_GET['orderTypeID'];
  switch($orderTypeID){
    case 1:
      $orderType = 'request';
      $pageTitle_ = 'チケット配布希望者';
      $message = "チケットを配布する内容を選んでください";
      break;
    case 3:
      $orderType = 'want_return';
      $pageTitle_ = "チケット返却希望者";
      $message = "チケットを受け取る内容を選んでください";
      break;
    case 4:
      $orderType = 'want_promotion';
      $pageTitle_ = "情宣希望者";
      $message = "チケットを渡す内容を選んでください";
      break;
    case 5:
      $orderType = 'sold_with_reserve';
      $pageTitle_ = "チケット預かり利用希望者";
      $message = "チケットを預かる内容を選んでください";
      break;
    case 7:
      $orderType = 'finish_promotion';
      $pageTitle_ = "情宣完了者";
      $message = "情宣用チケットを回収する内容を選んでください";
      break;
    default:
      //不正な操作が行われた時、それ相応の対応をする
      $_SESSION['tp_status'] = "invalid-page";
      header("Location: ../index.php");
      exit();
  }
