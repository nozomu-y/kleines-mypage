<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSecretSession();
  require_once(ROOT.'/view/header.php');
  getHeader("団員所持チケット総返却命令","secret");
?>
<h2>団員所持チケット総返却命令</h2>
<p>チケットを所持している全ての団員に、その枚数分の返却希望オーダーを強制的に注文させます</p>
<p>総返却命令を出しますか？</p>
<form method="post" action="<?=SERVER?>/model/allReturnHandler.php">
  <input type="hidden" name="submit" value="allReturn">
  <button class="btn btn-primary" type="submit">はい</button>
</form>
<br>
<p><a href="index.php">渉外チーフと秘密の部屋トップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>