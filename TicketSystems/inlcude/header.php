<html>
<head>
<title><?=$pageTitle ?? 'no title'?></title>
<link rel="stylesheet\" href="<?=SERVER."/views/css/bootstrap.min.css"?>">
<link rel="stylesheet" href="<?=SERVER."/views/css/$applyStyle.css"?>"> <!-- 読み込み先に応じて取得するスタイルシートを変更する -->
<link rel="stylesheet" href="<?=SERVER."/views/css/common.css"?>">
<link rel="stylesheet" href="<?=SERVER."/include/header.css"?>">
<link rel="stylesheet" href="<?=SERVER."/include/footer.css"?>">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<!-- header & navbar -->
<div id="wrapper">
  <div id="header" class="container-fruid">
    <div id="header-navbar">
      <?php require_once(ROOT."/include/navbar/$applyStyle.php"); ?>
    </div>
    <div id="header-title">
    <div id="pageTitle">header title</div>
    </div>
    <div id="header-account">
    <p>header account</p>
    </div>
  </div>
