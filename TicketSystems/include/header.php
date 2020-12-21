<?php
  if($pageTitle == ""){
    $pageTitle = "no title";
  }
?>

<html>
<head>
<title><?=SITE_NAME." - ".$pageTitle?></title>
<link rel="stylesheet\" href="<?=SERVER."/pages/css/bootstrap.min.css"?>">
<link rel="stylesheet" href="<?=SERVER."/pages/css/$applyStyle.css"?>"> <!-- 読み込み先に応じて取得するスタイルシートを変更する -->
<link rel="stylesheet" href="<?=SERVER."/pages/css/common.css"?>">
<link rel="stylesheet" href="<?=SERVER."/include/header.css"?>">
<link rel="stylesheet" href="<?=SERVER."/include/footer.css"?>">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
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
              <span id="cross1"></span><span id="cross2"></span>
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
    <?php if($_SESSION['tp_status'] != null): ?>
    <div class="status-area">
      <?= $_SESSION['tp_status']; ?>
    </div>
    <?php endif; ?>
    <h1><?=$pageTitle?></h1>