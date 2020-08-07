<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	require_once(ROOT.'/view/header.php');
	getHeader("団員用チケットページ","everyone");
?>
<?php
	
	if(isset($_SESSION['tp_status'])&&strcmp($_SESSION['tp_status'],"noAdmin")==0){
		//どこかのページで弾かれた時の表示内容
		echo "<p>限定ページに入る権限がありませんでした</p>";
		unset($_SESSION['tp_status']);
	}else if(isset($_SESSION['tp_status'])&&strcmp($_SESSION['tp_status'],"invalidPage")==0){
		//存在しないオーダーのページにアクセスした場合
		echo "<p>存在しないページです</p>";
		unset($_SESSION['tp_status']);
	}
?>
<h2>団員用チケットページ トップ</h2>
<h3>渉外からのお知らせ</h3>
<p>※権限によってここを書き換えられるようにしたい、wikiみたいなイメージ？hackMDの書式？(markDown?)</p>
<p>まだ実装されていません</p>
<br>
<h3>団員用メニュー</h3>
<p><a href="inputTicketAmount.php?orderType=1">チケットが欲しいとき</a></p>
<p><a href="soldTicket.php">チケットを売ったとき</a></p>
<p><a href="inputTicketAmount.php?orderType=3">チケットを渉外に返したいとき</a></p>
<p><a href="inputTicketAmount.php?orderType=6">チケットがお客様から返品されたとき</a></p>
<p><a href="registerPromotion.php">情宣のアポが取れたとき</a></p>
<p><a href="promotionList.php">情宣一覧確認</a></p>
<p><a href="history.php">自分のチケット状況・履歴確認</a></p>
<p>配布済座席確認(指定席限定)</p>
<br>
<h3>渉外用ページ</h3>
<p><a href="<?=SERVER?>/view/private/index.php">渉外用チケット管理ページトップへ(渉外限定)</a></p>
<br>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>
	