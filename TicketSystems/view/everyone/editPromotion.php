<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	$mysqli = dbconnect();

	//personIDが違った場合(代表者じゃなかった場合)、拒否
	$personID = h($_POST['personID']);
	if($personID!=$_SESSION['mypage_personID']){
		//弾く
		$_SESSION['tp_status'] = "notRepresent";
		header("Location: ".SERVER."/view/everyone/promotionList.php");
		exit();
	}
  
  require_once(ROOT.'/view/header.php');
	getHeader("情宣内容編集","everyone");
?>
<h2>情宣内容編集</h2>
<br>
<?php
	//memo 「代表者の変更」：相手のマイページでの承認後、代表者を変えることができる
  //orderIDを取得
	$orderID = h($_POST['orderID']);
  //一致するものをpromotionから探す
  $stmt = $mysqli->prepare("SELECT groupName,date FROM tp_Promotions WHERE orderID = ?");
  $stmt->bind_param('i',$orderID);
  $stmt->execute();
  $stmt->bind_result($gr,$date);
  $result = $stmt->fetch();
  if($result == NULL){
    //エラー処理
    $status = "FailSelByOrderID";
  }else{
    $status = "SucSelByOrderID";
  }
  if($stmt->fetch()!=NULL){
    $status .= "-OrderIDNotUnique";
  }
	$stmt->close();
	$timestamp=strtotime($date);
?>
<form action="promotionList.php" method="post" class="needs-validation" novalidate>
  <div class="form-group">
    <p class="componentsTitle">団体名</p>
		<input type="text" class="form-control 6 md-6" name="groupName" id="groupName" value="<?=h($gr)?>" required>
  </div>
  <div class="form-group">
		<p class="componentsTitle">日時はすでに決まっていますか？</p>
		<div class="custom-control custom-checkbox">
		<?php 
			echo "<input type='checkbox' class='custom-control-input' name='date_determined' id='date_determined' value='1'";
			if($date!=null){
				echo " checked";
			}
			echo ">";
		?>
			<label class="custom-control-label" for="date_determined">はい</label>
		</div>
	</div>
	<div class="form-group">
		<p class="componentsTitle">情宣日程</p>
		<?php //memo 選択済みの日程を編集する時の初期値を設定するjsを作れば早くなる？ ?>
	</div>
	<div class="form-row" id="date_select">
		<div class="form-group col-4 col-md-4">
			<label for="year">年</label>
			<select class="form-control" id="year" name="year">
        <?php
        if($date!=NULL){
          $selectYear = date('Y',$timestamp);
        }else{
          $selectYear = 0;
        }
				for($i=0;$i<2;$i++){
					$year = date('Y') + $i;
					echo "<option value=\"$year\"";
					if($selectYear!=0 && $selectYear==$year){
						echo " selected";
					}
					echo ">$year</option>";
				}
				?>
			</select>
		</div>
		<div class="form-group col-4 col-md-4">
			<label for="month">月</label>
			<select class="form-control" id="month" name="month">
      <?php
        if($date!=NULL){
          $selectMonth = date('n',$timestamp);
        }else{
          $selectMonth = 0;
				}
				
				for($i=1;$i<=12;$i++){
					$month = $i;
					echo "<option value=\"$month\"";
					if($selectMonth!=0 && $selectMonth==$month){
						echo " selected";
					}
					echo ">$month</option>";
				}
				?>
			</select>
		</div>
		<div class="form-group col-4 col-md-4">
			<label for="day">日</label>
			<select class="form-control" id="day" name="day">
        <?php
        if($date!=NULL){
          $selectDay = date('j',$timestamp);
        }else{
          $selectDay = 0;
        }
				for($i=1;$i<=31;$i++){
					$day = $i;
					echo "<option value=\"$day\"";
					if($selectDay!=0 && $selectDay==$day){
						echo " selected";
					}
					echo ">$day</option>";
				}
				//memo js使って日付バリデーション
				?>
			</select>
		</div>
  </div>
  <input type="hidden" name="orderID" value="<?=$orderID?>">
  <input type="hidden" name="change" value="change">
	<button type="submit" class="btn btn-primary">反映</button>
</form>
<script type="text/javascript" src="<?=SERVER?>/view/js/registerPromotion.js"></script>
<script type="text/javascript" src="<?=SERVER?>/view/js/formValidation.js"></script>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>