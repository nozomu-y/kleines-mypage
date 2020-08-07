<?php
  //セッションを使うことを宣言
  session_start();

  //ログインされていない場合は強制的にログインページにリダイレクト

  //セッション変数をクリア
  $_SESSION = array();

  //クッキーに登録されているセッションidの情報を削除
  if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
  }

  //セッションを破棄
  session_destroy();

  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
  require_once(ROOT.'/controller/functions.php');

  require_once(ROOT.'/view/header.php');
	getHeader("サインアウト","everyone");
?>
<h2>サインアウト</h2>
<p>サインアウトが完了しました。</p>
<br>
<p><a href="index.php">トップページへ</a></p>

<?php
	require_once(ROOT.'/view/footer.php');
	
?>