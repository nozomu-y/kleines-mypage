<?php
  require_once __DIR__.'/../include/tp_init.php';

  $_SESSION = array();  //セッション変数をクリア
  //クッキーに登録されているセッションidの情報を削除
  if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
  }
  session_destroy();  //セッションを破棄



  $pageTitle = "サインアウト";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">サインアウトしました</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>