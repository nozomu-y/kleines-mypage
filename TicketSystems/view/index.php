<?php
  session_start();
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
  require_once(ROOT.'/controller/functions.php');

  $mysqli=dbconnect();
  $q1 = "SHOW TABLES LIKE 'tp_Permissions'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result!=NULL && $result->num_rows==1){
    //本物のトップへ
    dbclose($mysqli);
    header("Location: ".SERVER."/view/everyone/index.php");
    exit();
  }
  
  require_once(ROOT.'/view/header.php');
  getHeader("待機部屋","everyone");
?>
<h2>仮トップ</h2>
<h3>本物のトップ</h3>
<p><a href="everyone/index.php">本物のトップへ</a></p>
<p>初期設定前だと初期設定を求めるようになっています</p>
<br>
<h3>サインイン</h3>
<p><a href="signIn.php">サインインへ</a></p>
<br>
<h3>秘密のパスワードによる初期設定(サインイン後)</h3>
<p><a href="<?=SERVER?>/view/secret/index.php">こちらから</a></p>

<?php
  require_once(ROOT.'/view/footer.php');
?>