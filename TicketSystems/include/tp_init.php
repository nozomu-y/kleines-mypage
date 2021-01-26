<?php
  require_once __DIR__ . "/../config/config.php";

  if(MODE === "staging" || MODE === "production"){
    require_once __DIR__ . "/../../Common/init_page.php";

  }else if(MODE === "develop"){
    //開発モード
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    ob_start();
    session_start();
    require_once __DIR__ . '/../../Common/dbconnect.php';
    require_once __DIR__ . '/../develop/User.php';
    require_once __DIR__ . '/accessFilter.php';

    if (!isset($_SESSION['mypage_email'])) {
      header('Location: ' . TP_SERVER . '/develop/signin.php');
      exit();
    }

    $email = $_SESSION['mypage_email'];
    $query = "SELECT * FROM members WHERE email='$email'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $USER = new User($result->fetch_assoc());
  }
  
  


  