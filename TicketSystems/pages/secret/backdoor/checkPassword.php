<?php
  /*
    パスワードと入力を比較
    OKだったら、tp_secret=="allowed"に設定
    NGだったら、tp_status = wrong-passwordでリダイレクト(放置でリダイレクトになる)
  */
  if(htmlspecialchars($_POST['password']) === SECRET_PASS){
    $_POST = array();
    $_SESSION['tp_secret'] = "allowed";
    $_SESSION['tp_status'] = "succeed-secret";
    header("Location: ".TP_SERVER."/pages/secret/initialSettings/index.php");
    exit();
  }else{
    $_POST = array();
    $_SESSION['tp_secret'] = "denied";
    $_SESSION['tp_status'] = "wrong-password";
  }