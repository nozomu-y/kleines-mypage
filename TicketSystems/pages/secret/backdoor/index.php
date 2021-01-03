<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  //ここにはaccessFilterを設定しない

  //パスワードの確認
  if(isset($_POST['process']) && strcmp($_POST['process'], "secret-pass")==0){
    require_once __DIR__ . "/checkPassword.php";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
  }

  $pageTitle = "パスワード入力";
  $applyStyle = "secret";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">初期設定のためのパスワードを入力してください。</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <input class="form-text" type="password" name="password" required>
  <input type="hidden" name="process" value="secret-pass">
  <button class="btn btn-primary" type="submit">送信</button>
</form>
<?php require_once TP_ROOT.'/include/footer.php'; ?>