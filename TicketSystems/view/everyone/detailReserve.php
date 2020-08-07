<?php
	//ini_set("display_errors",1);
  //error_reporting(E_ALL);
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
  $mysqli = dbconnect();

  $personID = h($_SESSION['mypage_personID']);
  $orderID = h($_POST['orderID']);
  $stmt = $mysqli->prepare("SELECT lastName,firstName,lastNameKana,firstNameKana,amount,response,price FROM tp_Reserves INNER JOIN tp_Orders USING(orderID) WHERE orderID = ?");
  $stmt->bind_param('i',$orderID);
  $stmt->execute();
  $stmt->bind_result($lastName,$firstName,$lastNameKana,$firstNameKana,$amount,$response,$price);
  $result = $stmt->fetch();
  if($result==NULL){
    echo "<!--invalid orderID-->";
    exit();
  }
  $stmt->close();

	require_once(ROOT.'/view/header.php');
  getHeader("チケット預かり詳細確認","everyone");
?>
<h2>チケット預かり詳細確認</h2>
<br>
<div class="row">
  <div class="col-4"><p>お客様氏名</p></div>
  <div class="col-8"><p><?=$lastName." ".$firstName?></p></div>
</div>
<div class="row">
  <div class="col-4"><p>お客様氏名(カナ)</p></div>
  <div class="col-8"><p><?=$lastNameKana." ".$firstNameKana?></p></div>
</div>
<div class="row">
  <div class="col-4"><p>枚数</p></div>
  <div class="col-8"><p><?=$amount?></p></div>
</div>
<div class="row">
  <div class="col-4"><p>値段</p></div>
  <div class="col-8"><p><?=$price?></p></div>
</div>
<br>
<div class="row detail-btns">
  <div class="col-2">
    <a class="btn btn-secondary btn-block" href="history.php" role="button">戻る</a>
  </div>
  <div class="col-2">
    <form action="changeReserve.php" method="post">
      <input type="hidden" name="orderID" value="<?=$orderID?>">
      <button type="submit" class="btn btn-primary btn-block">修正</button>
    </form>
  </div>
  <div class="col-2">
    <button type="button" id="btn-confirm" class="btn btn-danger btn-block" data-toggle="modal" data-target="#confirmModal">削除</button>
  </div>
</div>
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="label1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="label1">確認</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>この預かりを削除しますか？</p><br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">戻る</button>
        <form action="<?=SERVER?>/model/deleteReserveHandler.php" method="post">
					<input type="hidden" name="personID" value="<?=$personID?>">
					<input type="hidden" name="amount" value="<?=$amount?>">
					<input type="hidden" name="orderID" value="<?=$orderID?>">
					<input type="hidden" name="response" value="<?=$response?>">
					<button type="submit" class="btn btn-danger">削除</button>
				</form>
      </div>
    </div>
  </div>
</div>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>