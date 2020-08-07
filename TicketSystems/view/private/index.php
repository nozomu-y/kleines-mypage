<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	accessFilter();
  require_once(ROOT.'/view/header.php');
	getHeader("渉外用チケット管理ページトップ","private");
	
	if(isset($_SESSION['tp_status'])&&strcmp($_SESSION['tp_status'],"noAdmin")==0){
		echo "<p>限定ページに入る権限がありませんでした</p>";
		unset($_SESSION['tp_status']);
	}else if(isset($_SESSION['tp_status'])&&strcmp($_SESSION['tp_status'],"invalidPage")==0){
		//存在しないオーダーのページにアクセスした場合
		echo "<p>存在しないページです</p>";
		unset($_SESSION['tp_status']);
	}

?>
<h2>渉外用チケット管理ページ トップ</h2>
<h3>渉外用メニュー(練習日系)</h3>
<p><a href="dealTicket.php?ticketType=1">団員にチケットを配る</a></p>
<p><a href="dealTicket.php?ticketType=3">団員からチケットを返却してもらう</a></p>
<p><a href="dealTicket.php?ticketType=5">チケット預かり希望者からチケットを受け取る</a></p>
<p><a href="dealTicket.php?ticketType=4">情宣希望者にチケットを渡す</a></p>
<p><a href="dealTicket.php?ticketType=7">情宣終了者からチケットを回収する</a></p>
<p><a href="countTodayTickets.php">本日のチケット受け取り枚数確認</a></p>
<br>
<h3>確認系メニュー</h3>
<p><a href="memberTicketList.php">団員のチケット所持・配布状況を見る</a></p>
<p><a href="reserveList.php">チケット預かりリストを見る</a></p>
<p><a href="ticketInspection.php">チケット中間点検</a></p>
<p><a href="ticketAssign.php">チケット割り当ての確認・変更</a></p>
<p><a href="permissionList.php">役職メンバーの一覧・追加</a></p>
<br>
<h3>演奏会当日系メニュー</h3>
<p>未実装</p>
<br>
<h3>渉外チーフ限定ページ</h3>
<p><a href="<?=SERVER?>/view/secret/index.php">演奏会初期設定(チーフ限定)</a></p>
<br>
<h3>団員用ページ</h3>
<p><a href="<?=SERVER?>/view/everyone/index.php">団員用チケットページへ</a></p>
<br>
<?php
	require_once(ROOT.'/view/footer.php');
?>