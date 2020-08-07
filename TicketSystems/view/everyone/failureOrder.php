<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	require_once(ROOT.'/view/header.php');
	getHeader("申請失敗","everyone");
?>
<h2>申請失敗</h2>
<p>エラーが発生しました。お手数ですがもう一度やりなおしてください。</p>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>