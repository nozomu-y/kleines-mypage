<?php
  if(strcmp($pageTitle, "") == 0){
    $pageTitle = "no title";
  }
?>

<html>
<head>
<title><?=SITE_NAME." - ".$pageTitle?></title>
<link rel="stylesheet" href="<?=SERVER."/pages/css/$applyStyle.css"?>"> <!-- 読み込み先に応じて取得するスタイルシートを変更する -->
<link rel="stylesheet" href="<?=SERVER."/pages/css/common.css"?>">
<link rel="stylesheet" href="<?=SERVER."/include/header.css"?>">
<link rel="stylesheet" href="<?=SERVER."/include/footer.css"?>">
</head>
<body>
<!-- import js files -->
<script src="<?=SERVER?>/pages/js/jquery-3.5.1.min.js"></script>
<!-- header & navbar -->
<div id="wrapper">
  <div id="header" class="container-fruid">
    <div id="header-navbar">
      <div id="nav-drawer">
        <input id="nav-input" type="checkbox" class="nav-unshown">
        <label id="nav-open" for="nav-input"><span></span></label>
        <label id="nav-close" class="nav-unshown" for="nav-input"></label>
        <div id="nav-content">
          <div id="nav-header">
            <label id="nav-cross" for="nav-input">
              <span class="cross1"></span><span class="cross2"></span>
            </label>
          </div>
          <div id="nav-menu">
            <?php require_once(ROOT."/include/navbar/$applyStyle.php"); ?>
          </div>
        </div>
      </div>
    </div>
    <div id="header-title">
    <div id="page-title"><a href="<?=SERVER."/pages/$applyStyle/index.php"?>">CK_Ticket</a></div>
    </div>
    <div id="header-account">
    <p>header account</p>
    </div>
  </div>
  <div id="main">
  <div class="container">
    <?php if(isset($_SESSION['tp_status']) || isset($_POST['tp_status'])): ?>
    <div class="status-area">
      <?php
        if(isset($_SESSION['tp_status'])){
          echo($_SESSION['tp_status']."\n");
          unset($_SESSION['tp_status']);
        }
        if(isset($_POST['tp_status'])){
          echo($_POST['tp_status']."\n");
        }
      ?>
    </div>
    <?php endif; ?>
    <h1><?=$pageTitle?></h1>