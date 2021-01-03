<?php
  /*
    パスワードと入力を比較
    OKだったら、tp_secret=="allowed"に設定
    NGだったら、tp_status = wrong-passwordでリダイレクト(放置でリダイレクトになる)
  */
  if(strcmp(htmlspecialchars($_POST['pass']), SECRET_PASS)==0){
    $_POST = array();
    $_SESSION['tp_secret'] = "allowed";
    $_SESSION['tp_status'] = "succeed-secret";
    header("Location: ".TP_SERVER."/pages/secret/initialSettings/index.php");
    exit();
  }else{
    $_SESSION['tp_status'] = "wrong-password";
  }