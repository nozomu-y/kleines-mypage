<?php
	//ini_set("display_errors",1);
  //error_reporting(E_ALL);
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	$mysqli = dbconnect();

	require_once(ROOT.'/view/header.php');
	getHeader("手続き履歴確認","everyone");

	if(isset($_SESSION['mypage_status']) && strcmp($_SESSION['mypage_status'],"succeed_delete")==0){
		echo "<p>チケット預かりの取り消しを完了しました</p>";
		unset($_SESSION['mypage_status']);
	}else if(isset($_SESSION['mypage_status']) && strcmp($_SESSION['mypage_status'],"cancelOrder")==0){
		echo "<p>オーダーの取り消しを完了しました</p>";
		unset($_SESSION['mypage_status']);
	}
	//中身が設定されているチケット預かりのorderIdの一覧を取得
	$sql = "SELECT orderID FROM tp_Reserves";
	$result = $mysqli->query($sql);
	if($result == NULL){
		echo "<!-- fail select : code : $mysqli->error -->";
		exit();
	}
	$reserveIDs = array();
	//連想配列で取得
	while($row = $result->fetch_array(MYSQLI_NUM)){
		$reserveIDs[] = (int) $row[0];
	}
	//結果セットを解放
	$result->free();
?>
<h2>チケット状況・履歴確認</h2>
<h3>チケット状況確認</h3>
<?php
	//その人の所持数・販売枚数をtp_MemberTicketsから取得
	$stmt = $mysqli->prepare("SELECT have,sold FROM tp_MemberTickets WHERE personID = ?");
	$stmt->bind_param('i',h($_SESSION['mypage_personID']));
	$stmt->execute();
	$stmt->bind_result($hav,$sol);
	$result = $stmt->fetch();
	if($result==NULL){
		echo "<!--noPersonID-->";
		exit();
	}
	$have = $hav;
	$sold = $sol;
	$stmt->close();
?>
<div class="table-responsive">
	<table class="table table-striped text-nowrap">
		<tr>
			<th class="have">所持枚数</th>
			<td class="have"><?=$have?></td>
			<th class="sold">販売枚数</th>
			<td class="sold"><?=$sold?></td>
		</tr>
	</table>
</div>
<br>
<h3>履歴確認</h3>
<p>預かり内容の変更・削除は、「詳細」から行ってください</p>
<div class="table-responsive">
	<table class='table text-nowrap table-striped'>
		<tr>
			<th class="orderID">orderID</th>
			<th class="orderType">orderType</th>
			<th class="amount">枚数</th>
			<th class="orderTime">注文時刻</th>
			<th class="finishFlag">完了フラグ</th>
			<th class="finishTime">完了時刻</th>
			<th class="details">詳細・取消</th>
		</tr>
		<?php
			$id = h($_SESSION['mypage_personID']);
			//そのpersonIDの人の全てのorderを取得
			$stmt = $mysqli->prepare("SELECT orderID,personID,orderTypeID,orderTypeName,amount,response,orderTime,finishFlag,finishTime FROM tp_Orders INNER JOIN tp_OrderTypes USING(orderTypeID) WHERE personID = ?");
			$stmt->bind_param('i',$id);
			$stmt->execute(); //SQLの実行
			$stmt->bind_result($orderID,$personID,$orderTypeID,$orderType,$amount,$response,$orderTime,$finishFlag,$finishTime);
			while($result = $stmt->fetch()):
		?>
		<tr>
			<td class="orderID"><?=$orderID?></td>
			<td class="orderType"><?=$orderType?></td>
			<td class="amount"><?=$amount?></td>
			<td class="orderTime"><?=$orderTime?></td>
			<td class="finishFlag"><?=$finishFlag?></td>
			<td class="finishTime"><?=$finishTime?></td>
			<td class="details">
				<?php if($orderTypeID==5 && in_array($orderID,$reserveIDs,TRUE)):	//orderが預かり利用販売だった場合で、削除されていない預かりデータの場合 ?>
					<form action='detailReserve.php' method='post'>
						<input type='hidden' name='orderID' value='<?=$orderID?>'>
						<input type='submit' class='btn btn-primary btn-sm' value='詳細'>
					</form>
				<?php elseif($finishFlag==0&&($orderTypeID==1||$orderTypeID==3)): //未完了のオーダー ?>
					<button type="button" id="btn-confirm" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmModal">
						取消
					</button>
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
									<p>この注文を取り消しますか？</p><br>
									<div class="row">
										<div class="col-4"><p>注文者</p></div>
										<div class="col-8"><p>
											<?=$_SESSION['grade'].$_SESSION['part']." ".$_SESSION['lname'].$_SESSION['fname']?>
										</p></div>
									</div>
									<div class="row">
										<div class="col-4"><p>オーダー種別</p></div>
										<div class="col-8"><p>
											<?php
												if($orderTypeID==1){  //request
													echo "チケット希望";
												}else if($orderTypeID==3){  //want_return
													echo "チケット返却希望";
												}
											?>
										</p></div>
									</div>
									<div class="row">
										<div class="col-4"><p>枚数</p></div>
										<div class="col-8"><p><?=$amount?></p></div>
									</div>
									<div class="row">
										<div class="col-4"><p>注文日時</p></div>
										<div class="col-8"><p><?=$orderTime?></p></div>
									</div>
								</div>
								<div class="modal-footer">
								<form action="<?=SERVER?>/model/cancelOrderHandler.php" method="post">
									<input type="hidden" name="orderID" value="<?=$orderID?>">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">戻る</button>
									<button type="submit" class="btn btn-danger">取り消す</button>
								</form>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</td>
		</tr>
		<?php endwhile;
			if($result == NULL){	//表示されない場合、オーダーがないだけかエラーが起きているのかを判別する
				echo "<!-- no tp_Orders -->";
			}else if($result == false){
				echo "<!--error : $mysqli->error -->";
			}
			$stmt->close();
		?>
	</table>
</div>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>