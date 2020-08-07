<?php 
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  session_start();
  if (!isset($_SESSION["mypage_email"])) {  //未ログイン
    header("Location: ".SERVER."/view/signIn.php");
    exit();
  }
  if(isset($_SESSION['tp_secret'])){
    unset($_SESSION['tp_secret']);
  }
  require_once(ROOT.'/view/header.php');
  getHeader("パスワードを入力","secret");
?>
<p>パスワードを入力してください</p>
<form action="<?=SERVER?>/model/secretPassword.php" method="post">
  <div class="form-group">
    <input class="form-control" type="password" name="pass">
  </div>
  <button type="submit" class="btn btn-primary">送信</button>
</form>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>