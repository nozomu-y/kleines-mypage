<?php
  require_once __DIR__.'/../include/tp_init.php';
  //ここにはフィルターをつけない

  //初期化する
  if(isset($_POST['process']) && $_POST['process'] === "init"){
    require_once __DIR__ . "/easyInit.php";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
  }

  $pageTitle = "初期化";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">ワンプッシュで初期状態にできます。</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <input type="hidden" name="process" value="init">
  <button type="submit" class="btn btn-danger">初期化する</button>
</form>
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>