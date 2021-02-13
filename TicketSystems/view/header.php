<?php
function getHeader($pageTitle,$mode){
  echo "<html>"
      ."<head>"
      ."<title>$pageTitle</title>";
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT."/controller/functions.php");
  importBootstrap();
  if(strcmp($mode,"everyone")==0){
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".SERVER."/view/css/everyone.css\">";
  }else if(strcmp($mode,"private")==0){
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".SERVER."/view/css/private.css\">";
  }else if(strcmp($mode,"secret")==0){
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".SERVER."/view/css/secret.css\">";
  }
  echo "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>";  //もしかしたら不要？
  echo "</head>"
  ."<body>"
    ."<div id=\"wrapper\">"
      ."<div id=\"header\" class=\"sticky-top\">"
        ."<div class=\"container-fluid\">"
          ."<ul class=\"navbar\">"
            ."<li class=\"navbar-title\">"
              ."<h2><a href=\"index.php\">";
          if(strcmp($mode,"everyone")==0){
            echo "団員用チケットページ";
          }else if(strcmp($mode,"private")==0){
            echo "渉外用チケット管理ページ";
          }else if(strcmp($mode,"secret")==0){
            echo "一部の人専用ページ";
          }
          echo "</a></h2>"
              ."</li>"
              ."<li class=\"navber-welcome\">"
                ."<p>ようこそ ".$_SESSION['grade'].$_SESSION['part']." ".$_SESSION['lname'].$_SESSION['fname']." さん</p>"
              ."</li>"
              ."<li><p>権限：".$_SESSION['tp_permission']."</p></li>"
              ."<li>"
                ."<a href=\"".SERVER."/view/signOut.php\">サインアウト</a>"
              ."</li>"
        ."</div></div>"
      ."<div class=\"container-fluid\" id=\"main\">";
}

?>

