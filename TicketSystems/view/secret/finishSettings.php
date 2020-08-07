<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSecretSession();
?>
<?php
  require_once(ROOT.'/view/header.php');
  getHeader("設定完了","secret");
?>
<h2>設定完了</h2>
<p>初期設定が完了しました。</p>
<br>
<p><a href="index.php">渉外チーフと秘密の部屋トップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>