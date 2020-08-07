<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	$mysqli = dbconnect();

	//personIDが違った場合(代表者じゃなかった場合)、拒否
	$personID = h($_SESSION['mypage_personID']);
	$orderID = h($_POST['orderID']);

	//DBで検索
	$stmt = $mysqli->prepare("SELECT lastName,firstName,lastNameKana,firstNameKana,price,amount,response FROM tp_Reserves INNER JOIN tp_Orders USING(orderID) WHERE orderID = ?");
	$stmt->bind_param('i',$orderID);
	$stmt->execute();
	$stmt->bind_result($lname,$fname,$lnameKana,$fnameKana,$price,$amount,$response);
	$result = $stmt->fetch();
	if(!$result){
		echo "<!-- fail select from reserve : ".$result->error." -->";
	}
	$stmt->close();

	require_once(ROOT.'/view/header.php');
	getHeader("チケット預かり内容変更","everyone");
?>
<h2>チケット預かり内容変更</h2>
<br>
<form method="post" action="<?=SERVER?>/model/changeReserveHandler.php">
	<p class="componentsTitle">お客様の情報を入力してください</p>
	<div class="form-row">
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">姓</p>
			<input type="text" class="form-control 6 md-6" name="lname" id="lname" value="<?=h($lname)?>" required>
			<div class="invalid-feedback">入力してください</div>
		</div>
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">名</p>
			<?php
				echo "<input type=\"text\" class=\"form-control 6 md-6\" name=\"fname\" id=\"fname\"";
				if($fname!=NULL){
					echo " value=\"";
					echo h($fname);
					echo "\"";
				}
				echo " required>";
			?>
			<div class="invalid-feedback">入力してください</div>
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">姓(カナ)</p>
			<input type="text" class="form-control 6 md-6" name="lnameKana" id="lnameKana" value="<?=h($lnameKana)?>" required>

			<div class="invalid-feedback">入力してください</div>
		</div>
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">名(カナ)</p>
			<?php
				echo "<input type=\"text\" class=\"form-control 6 md-6\" name=\"fnameKana\" id=\"fnameKana\"";
				if(!empty($fnameKana)){
					echo " value=\"";
					echo h($fnameKana);
					echo "\"";
				}
				echo " required>";
			?>
			<div class="invalid-feedback">入力してください</div>
		</div>
	</div>
	<div class="form-row">
		<div class="form-text text-muted">
		<p class="helptext">氏名にカタカナやアルファベットを利用する場合でも、カナの入力をお願いします</p>
			</div>
	</div>
	<div class="form-row">
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">利用枚数</p>
			<input type="text" class="form-control 6 md-6" name="amount" id="amount" value="<?=$amount?>" required>
			<div class="invalid-feedback">入力してください</div>
		</div>
		<?php //memo:数値バリデーションテェック ?>
	</div>
	<div class="form-row">
		<div class="form-text text-muted">
			<p class="helptext">代表者に渡す場合はグループの人数を、1人ずつの場合には1を入力してください</p>
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">値段(500円単位のみ)</p>
			<input type="text" class="form-control 6 md-6" name="price" id="price" value="<?=$price?>" required>
			<div class="invalid-feedback">入力してください</div>
		</div>
	</div>
	<input type="hidden" name="orderID" value="<?=$orderID?>">
	<button type="submit" class="btn btn-primary">変更する</button>
</form>
<br>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<script type="text/javascript" src="<?=SERVER?>/view/js/formValidation.js"></script>
<?php
	require_once(ROOT.'/view/footer.php');
?>