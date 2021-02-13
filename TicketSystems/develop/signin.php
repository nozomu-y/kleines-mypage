<?php
  //require_once __DIR__.'/../include/tp_init.php';
  require_once __DIR__ . "/../config/config.php";
  ini_set("display_errors",1);
  error_reporting(E_ALL);
  ob_start();
  session_start();

  $pageTitle = "サインイン/メンバー登録";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">開発用ログインページ</p>
<h2>サインイン</h2>
<form action="signinHandler.php" method="post">
  <p class="tx">メールアドレス</p>
  <input class="form-text" type="email" name="email" required>
  <p class="tx">パスワード</p>
  <input class="form-text" type="password" name="password" required>
  <button type="submit" class="btn btn-primary">Sign In!</button>
</form>
<h2>メンバー登録</h2>
<p class="tx">sub title</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>