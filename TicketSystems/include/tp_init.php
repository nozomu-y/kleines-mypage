<?php
  require_once __DIR__ . "/../config/config.php";

  if(MODE === "production"){
    //本番環境
    require_once __DIR__ . "/../../Common/init_page.php";
  }else if(MODE === "staging"){
    //ステージング環境
    //接続するDBを変えたりする場合は、ここかTicketSystems/Config/configで変更する
    //接続DBを変える場合、membersテーブルが必要になる
    //→DB接続し、tp_createMembersを呼び出すと、作成可能

    //現状は本番環境と同じDBを読み込む
    require_once __DIR__ . "/../../Common/init_page.php";

  }else if(MODE === "develop"){
    //開発環境
    ini_set("display_errors",　1);
    error_reporting(E_ALL);
    ob_start();
    session_start();

    //開発環境の時は、開発用DBにアクセス
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if($mysqli->connect_error){
      error_log($mysqli->connect_error);
      exit;
    }

    $query = "SHOW TABLES";
    $result = $mysqli->query($query);
    if(!$result){
      print('Query Failed : ' . $mysqli->error);
      $mysqli->close();
      exit();
    }

    if($result->fetch_assoc() == null){
      print('You need to initialize MySQL table.');
      exit();
    }
    require_once __DIR__ . '/../develop/User.php';
    require_once __DIR__ . '/accessFilter.php';

    if(!isset($_SESSION['mypage_email'])){
      header('Location: ' . TP_SERVER . '/develop/signin.php');
      exit();
    }

    $email = $_SESSION['mypage_email'];
    $query = "SELECT * FROM members WHERE email='$email'";
    $result = $mysqli->query($query);
    if(!$result){
      print('Query Failed : ' . $mysqli->error);
      $mysqli->close();
      exit();
    }
    $USER = new User($result->fetch_assoc());
  }
  
  


  