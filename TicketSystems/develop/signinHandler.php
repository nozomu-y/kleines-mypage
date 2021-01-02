<?php
  //DB接続
  session_start();
  ini_set("display_errors",1);
  error_reporting(E_ALL);
  require_once __DIR__ . "/../config/config.php";
  require_once __DIR__ . "/../../Common/dbconnect.php";

  //post送信されてきたユーザー名がデータベースにあるか検索
  $stmt = $mysqli->prepare('SELECT id,password,last_name,first_name,part,grade FROM members WHERE email = ?');
  $stmt->bind_param('s',$_POST['email']);  //
  $stmt->execute(); //SQLの実行
  $stmt->bind_result($id,$pass,$lname,$fname,$part,$grade);
  $result = $stmt->fetch();
  if($result == NULL){
    $_SESSION['tp_error'] = "wrong-email";
    $stmt->close();
    header("Location: ".TP_SERVER."/develop/signin.php");
    exit();
  }
  $stmt->close();

  //検索したユーザー名に対してパスワードが正しいかを検証
  //正しくないとき
  if(!password_verify($_POST['password'],$pass)){
    $_SESSION['tp_error'] = "wrong-password";
    $stmt->close();
    header("Location: ".TP_SERVER."/develop/signin.php");
    exit();
  }else{
    session_regenerate_id(TRUE);  //session idを再発行
    $_SESSION['mypage_email'] = htmlspecialchars($_POST['email']);
    $_SESSION['tp_status']="succeed_signin";
    $stmt->close();
    header("Location: ".TP_SERVER."/develop/signin.php");
    exit();
  }
?>