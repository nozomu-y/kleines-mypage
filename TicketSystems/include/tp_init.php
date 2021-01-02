<?php
  require_once __DIR__ . "/../config/config.php";

  if(strcmp(MODE,"real") == 0){
    require_once __DIR__ . "/../../Common/init_page.php";

  }else if(strcmp(MODE,"test") == 0){
    //開発モード
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    ob_start();
    session_start();
    require_once __DIR__ . '/../../Common/dbconnect.php';
    require_once __DIR__ . '/../develop/User.php';

    $email = $_SESSION['mypage_email'];
    $query = "SELECT * FROM members WHERE email='$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $USER = new User($result->fetch_assoc());

    if (!isset($_SESSION['mypage_email'])) {
      header('Location: ' . TP_SERVER . '/develop/signin.php');
      exit();
    }
  }
  
  


  