<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSecretSession();
  require_once(ROOT.'/view/header.php');
  getHeader("渉外チーフと秘密の部屋トップ","secret");
  if(isset($_SESSION['tp_status'])&&strcmp($_SESSION['tp_status'],"succeed")==0){
    echo "<p>パスワードによる認証に成功しました</p>";
  }
?>
<h2>渉外チーフと秘密の部屋 トップ</h2>
<p>渉外チーフとWeb管しか入れない秘密の部屋です</p>
<p>そんな別に大したものは入ってない</p>
<br>
<p><a href="initialSettings.php">(取扱注意)演奏会チケット情報初期化</a></p>
<br>
<p><a href="<?=SERVER?>/view/private/index.php">渉外用チケット管理ページへ</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>