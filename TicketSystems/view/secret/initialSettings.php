<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSecretSession();
  require_once(ROOT.'/view/header.php');
  getHeader("演奏会初期設定(工事中)","secret");
?>
<h2>演奏会初期設定</h2>
<p>現在の挙動：<br>
・tp_MemberTickets,tp_TicketTotal,tp_Reserves,tp_Responses,tp_Promotions,tp_Orders,tp_Permissionsを<br>
  存在していなかったら作成<br>
  存在していたら、中身を空にする<br>
・membersから、全団員のIDを抽出してtp_MemberTicketに挿入(have,soldは0)<br>
・チケット割り当て設定<br>
・渉外メンバー、チーフ等要職などの設定、権限振り分け
</p>
<p>ゆくゆくの予定：<br>
　・全チケット枚数の登録、番号振り分け<br>
　・指定席か自由席か、指定席の場合は座席番号とチケットの対応、座席表など<br>
　・渉外用のメールアドレスの登録
　・etc<br>
</p>
<p>現在の挙動で初期設定を行いますか？</p>
<form method="post" action="ticketSettings.php">
  <input type="hidden" name="submit" value="init">
  <button class="btn btn-primary" type="submit">Yes</button>
</form>
<br>
<p><a href="index.php">渉外チーフと秘密の部屋トップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>