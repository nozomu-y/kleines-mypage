<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	require_once(ROOT.'/controller/orderController.php');
	assignOrder($_POST['orderType']);
?>
<?php
	require_once(ROOT.'/view/header.php');
	getHeader("申請完了","everyone");
?>
<h2>申請完了</h2>
<p>申請が完了しました。</p>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>