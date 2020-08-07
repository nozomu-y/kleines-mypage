<?php 
  //ini_set("display_errors",1);
  //error_reporting(E_ALL);
  //DBへの接続
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  //startSession();
  session_start();
  $mysqli = dbconnect();

  //ユーザー情報を取得(Email,pass)
  $email = h($_POST['email']);
  $pass = h($_POST['password']);
  $pass_conf=h($_POST['password-confirm']);
  $part = h($_POST['part']);
  $grade = h($_POST['grade']);
  $fname = h($_POST['first_name']);
  $lname = h($_POST['last_name']);
  $message = "";  //for debug
  //passとpass-confが一致しているかを確認
  if(strcmp($pass,$pass_conf)!=0){
    //戻す
    $_SESSION['mypage_register_error'] = "pass-conf-error";
    header("Location: ".SERVER."/view/signIn.php?status=registerError");
    exit();
  }

  //passをハッシュ化
  $pw = password_hash($pass,PASSWORD_DEFAULT);

  //ユーザー情報エラーチェック
  //このユーザーがいないかを調べる
  $stmt = $mysqli->prepare("SELECT * FROM members WHERE email = ?");
  $stmt->bind_param('s',$email);  //emailをバインド
  $stmt->execute(); //SQLの実行
  $result = $stmt->get_result()->fetch_row();
  if($result == NULL){ //0行：被りなし
    $message.= "this-email-can-be-used.";
    //テーブルに挿入
    $stmt = $mysqli->prepare("INSERT INTO members (email,password,part,grade,first_name,last_name) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param('sssiss',$email,$pw,$part,$grade,$fname,$lname);
    $res = $stmt->execute();
    if($res){
      $insert = $stmt->affected_rows;
      $message.= $insert."-rows-affected.";
      $message.= "登録完了しました。";
      $stmt->close();

      //tp_MemberTicketsが存在する場合、tp_MemberTicketsに挿入
      $q1 = "SHOW TABLES LIKE 'tp_MemberTickets'"; //完全一致でテーブルを検索
      $result2 = $mysqli->query($q1);
      if($result2 != NULL && $result2->num_rows==1){
        $personID = $mysqli->insert_id; //personIDを取得
        $stmt = $mysqli->prepare("INSERT INTO tp_MemberTickets (personID) VALUES (?)");
        $stmt->bind_param('i',$personID);
        $res_mt = $stmt->execute();
        if($res_mt){
          $message .= "succeed-insert-memberTicket";
        }else{
          $message .= "fail-to-insert-memberTicket";
        }
        $stmt->close();
      }
    }else{
      $message.= "fail-to-execute.";
      $stmt->close();
    }
  }else{  //被りあり
    $_SESSION['mypage_register_error'] = "exist-email";
    header("Location: ".SERVER."/view/signIn.php");
    dbclose($mysqli);
    exit();
  }
  dbclose($mysqli);
  unset($_SESSION['mypage_register_error']);
  header("Location: ".SERVER."/view/signIn.php?status=succeedSignUp");
  exit();
?>