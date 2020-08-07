<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	accessFilter();
	require_once(ROOT.'/view/header.php');
	getHeader("処理失敗","private");
?>
<h2>処理失敗</h2>
<p>エラーが発生しました。お手数ですがもう一度やりなおしてください。</p>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>