<?php
  require_once __DIR__ . "/../config/config.php";

  if(strcmp(MODE,"real") == 0){
    require_once __DIR__ . "/../../Common/init_page.php";
  }else if(strcmp(MODE,"test") == 0){
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    ob_start();
    session_start();
    require __DIR__ . '/../../Common/dbconnect.php';
  }
  
  


  