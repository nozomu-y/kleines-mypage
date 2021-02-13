<?php
  //関数を定義するファイル
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  //db接続
  function dbconnect(){
    if(MODE === "develop"){
      $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
      //接続エラーチェック
      if($mysqli->connect_error){
        echo $mysqli->connect_error;
        exit();
      }else{
        $mysqli->set_charset("utf8");
      }
    
    }else if(MODE === "staging" || MODE === "production"){
      require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
    }
    return $mysqli;
  }
  //db接続を閉じる
  function dbclose($mysqli){
    $mysqli->close();
    //echo "db closed";
  }

  //htmlspecialchars
  function h($s){
    return htmlspecialchars($s,ENT_QUOTES);
  }

  //セッション開始とリダイレクト
  function startSession(){
    session_start();
    if (!isset($_SESSION["mypage_email"])) {  //未ログイン
      header("Location: ".SERVER."/view/signIn.php");
      exit();
    }
    if(isset($_SESSION['tp_secret'])){
      unset($_SESSION['tp_secret']);
    }
    $mysqli = dbconnect();
    if(!isset($_SESSION["mypage_personID"])){
      //検索して取得
      $stmt = $mysqli->prepare('SELECT personID,password,last_name,first_name,part,grade FROM members WHERE email = ?');
      $stmt->bind_param('s',h($_SESSION['mypage_email']));  //
      $stmt->execute(); //SQLの実行
      $stmt->bind_result($id,$pass,$lname,$fname,$part,$grade);
      $result = $stmt->fetch();
      $_SESSION['mypage_personID'] = h($id);
      $_SESSION['grade'] = h($grade);
      $_SESSION['part'] = h($part);
      $_SESSION['fname'] = h($fname);
      $_SESSION['lname'] = h($lname);
      $stmt->close();
    }
    if(!isset($_SESSION['tp_permission'])){
      //permissionを取得、テーブルがなかったら初期設定画面へ
      $q1 = "SHOW TABLES LIKE 'tp_Permissions'"; //完全一致でテーブルを検索
      $result = $mysqli->query($q1);
      if($result==NULL || $result->num_rows==0){
        //初期設定画面へ
        dbclose($mysqli);
        header("Location: ".SERVER."/view/secret/index.php");
        exit();
      }else{
        $result->free();
        //permissionを取得
        $stmt = $mysqli->prepare('SELECT permission FROM tp_Permissions WHERE personID = ?');
        $stmt->bind_param('i',$_SESSION['mypage_personID']);
        $stmt->execute();
        $stmt->bind_result($permission);
        $result = $stmt->fetch();
        if($result){
          $_SESSION['tp_permission'] = $permission;
        }else{
          $_SESSION['tp_permission'] = 999; //なんの権限もない数値
        }
      }
    }
    dbclose($mysqli);
  }

  function accessFilter(){
    if(session_status() == PHP_SESSION_NONE){
      startSession();
    }
    //permissionが20以上の人は弾く
    //13~19は未割り当て
    //一時許可を設定する？
    //売り上げ状況だけ見れる人とか
    if($_SESSION['tp_permission']>=20){
      $_SESSION['tp_status'] = "noAdmin";
      header("Location: ".SERVER."/view/everyone/index.php");
      exit();
    }
  }

  function startSecretSession(){
    session_start();
    if (!isset($_SESSION["mypage_email"])) {  //未ログイン
      header("Location: ".SERVER."/view/signIn.php");
      exit();
    }
    $mysqli = dbconnect();
    if(!isset($_SESSION["mypage_personID"])){
      //検索して取得
      $stmt = $mysqli->prepare('SELECT personID,password,last_name,first_name,part,grade FROM members WHERE email = ?');
      $stmt->bind_param('s',h($_SESSION['mypage_email']));  //
      $stmt->execute(); //SQLの実行
      $stmt->bind_result($id,$pass,$lname,$fname,$part,$grade);
      $result = $stmt->fetch();
      $_SESSION['mypage_personID'] = h($id);
      $_SESSION['grade'] = h($grade);
      $_SESSION['part'] = h($part);
      $_SESSION['fname'] = h($fname);
      $_SESSION['lname'] = h($lname);
      $stmt->close();
    }
    if(!isset($_SESSION['tp_secret'])){ //tp_secretが設定されていないとき
      if(!isset($_SESSION['tp_permission'])||$_SESSION['tp_permission']>=12){
        //パスワード入力画面に飛ぶ
        dbclose($mysqli);
        header("Location: ".SERVER."/view/secret/inputPass.php");
        exit();
      }else if($_SESSION['tp_permission'] <= 11){
        $_SESSION['tp_secret'] = "OK";
      }
    }
    if($_SESSION['tp_secret'] !== "OK"){ //tp_secretが設定された上で許可されてない時
      //弾く
      $_SESSION['tp_status'] = "noAdmin";
      unset($_SESSION['tp_secret']);
      dbclose($mysqli);
      header("Location: ".SERVER."/view/private/index.php");
      exit();
    }    
    dbclose($mysqli);
  }

  //bootstrapをインポートする
  function importBootstrap(){
    //echo "<link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css\" integrity=\"sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk\" crossorigin=\"anonymous\">";
    echo "<link rel=\"stylesheet\" href=\"".SERVER."/view/css/bootstrap.min.css\">";
  }
?>