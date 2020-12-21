<?php
  //GETメソッドでIDを取得
  //許可されたIDの場合、それ用の操作を行う
  //IDごとにpageType用の変数を振り分ける
  if($_GET['orderType'] == 1){
    $orderType = 'request';
    $pageTitle_ = 'チケット希望フォーム';
  }else if($_GET['orderType']==3){
    $orderType = 'want_return';
    $pageTitle_ = "チケット返却希望フォーム";
  }else if($_GET['orderType']==6){
    $orderType = 'cancel';
    $pageTitle_ = "チケットキャンセル報告";
  }else{
    //不正な操作が行われた時、それ相応の対応をする
    $_SESSION['tp_status'] = "invalidPage";
    header("Location: ../index.php");
    exit();
  }

?>