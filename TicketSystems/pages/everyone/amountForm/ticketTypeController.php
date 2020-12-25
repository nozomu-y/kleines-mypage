<?php
  //GETメソッドでIDを取得
  //許可されたIDの場合、それ用の操作を行う
  //IDごとにpageType用の変数を振り分ける
  $orderTypeID = $_GET['orderTypeID'];
  if($orderTypeID == 1){
    $orderType = 'request';
    $pageTitle_ = 'チケット希望フォーム';
    $message = '欲しい枚数を入力してください';
  }else if($orderTypeID == 3){
    $orderType = 'want_return';
    $pageTitle_ = "チケット返却希望フォーム";
    $message = '返却する枚数を入力してください';
  }else if($orderTypeID == 6){
    $orderType = 'cancel';
    $pageTitle_ = "チケットキャンセル報告";
    $message = "返品された枚数を入力してください";
  }else{
    //不正な操作が行われた時、それ相応の対応をする
    $_SESSION['tp_status'] = "invalidPage";
    header("Location: ../index.php");
    exit();
  }
