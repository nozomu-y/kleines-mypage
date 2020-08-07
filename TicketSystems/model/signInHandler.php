<?php
  //DB接続
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  $mysqli = dbconnect();
  session_start();

  //post送信されてきたユーザー名がデータベースにあるか検索
  $stmt = $mysqli->prepare('SELECT personID,password,last_name,first_name,part,grade FROM members WHERE email = ?');
  $stmt->bind_param('s',h($_POST['email']));  //
  $stmt->execute(); //SQLの実行
  $stmt->bind_result($id,$pass,$lname,$fname,$part,$grade);
  $result = $stmt->fetch();
  if($result == NULL){
    $_SESSION['mypage_auth_error'] = "wrong-email";
    header("Location: ".SERVER."/view/signIn.php");
    exit();
  }
  $stmt->close();

  //検索したユーザー名に対してパスワードが正しいかを検証
  //正しくないとき
  if(!password_verify($_POST['password'],$pass)){
    $_SESSION['mypage_auth_error'] = "wrong-password";
    header("Location: ".SERVER."/view/signIn.php");
    exit();
  }else{
    session_regenerate_id(TRUE);  //session idを再発行
    $_SESSION['mypage_email'] = h($_POST['email']);
    $_SESSION['mypage_personID'] = h($id);
    $_SESSION['grade'] = h($grade);
    $_SESSION['part'] = h($part);
    $_SESSION['fname'] = h($fname);
    $_SESSION['lname'] = h($lname);
    $_SESSION['tp_status']="succeedSignIn";
    header("Location: ".SERVER."/view/signIn.php");
    exit();
  }
?>