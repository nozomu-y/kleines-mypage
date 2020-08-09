<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSecretSession();

  require_once(ROOT.'/view/header.php');
  getHeader("完了","secret");
?>
<h2>完了</h2>
<p>全ての動作が完了しました。</p>
<br>
<p><a href="index.php">渉外チーフと秘密の部屋トップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>