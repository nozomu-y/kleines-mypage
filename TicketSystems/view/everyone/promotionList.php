<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	$mysqli = dbconnect();

	if($_POST['change']=="change"){
		//変更処理
		require_once(ROOT.'/model/promotionEditer.php');
		$_SESSION['tp_status']="changed";
		header("Location:".$_SERVER['PHP_SELF']);
    exit();
	}
	
	require_once(ROOT.'/view/header.php');
	getHeader("情宣一覧","everyone");
?>
<?php
	if(isset($_SESSION['tp_status'])&&strcmp($_SESSION['tp_status'],"notRepresent")==0){
		echo "<p>代表者以外の情報の編集・完了報告はできません</p><br>";
		unset($_SESSION['tp_status']);
	}else if(isset($_SESSION['tp_status'])&&strcmp($_SESSION['tp_status'],"changed")==0){
		echo "<p>内容を変更しました</p><br>";
		unset($_SESSION['tp_status']);
	}
?>
<h2>情宣一覧</h2>
<br>
<?php
	//情宣一覧を検索
	$sql = "SELECT tp_Orders.personID,members.grade,members.last_name,members.first_name,members.part,tp_Orders.orderID,groupName,date,tp_Promotions.finishFlag FROM tp_Promotions INNER JOIN tp_Orders ON tp_Promotions.orderID = tp_Orders.orderID INNER JOIN members ON members.personID = tp_Orders.personID ORDER BY date";
  $result = $mysqli->query($sql);
  if($result == NULL){
    echo "<!-- fail select : code : $mysqli->error -->";
    exit();
  }
  //連想配列で取得
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $rows[] = $row;
  }
  //結果セットを解放
	$result->free();
?>
<div class="table-responsive-md">
	<table class='table-sm text-nowrap table-striped' id="promotionTable">
		<tr>
			<th class="grade">学年</th>
			<th class="part">パート</th>
			<th class="last_name">姓</th>
			<th class="first_name">名</th>
			<th class="groupName">団体名</th>
			<th class="date">日程</th>
			<th class="finishFlag">状態</th>
			<th class="edit"></th>
			<th class="done"></th>
		</tr>
		<?php foreach($rows as $row): ?>
		<tr>
			<?php //<td class="personID"><?php echo $row['personID'];?\> </td> ?>
			<td class="grade"><?php echo h($row['grade']); ?></td>
			<td class="part"><?php echo h($row['part']); ?></td>
			<td class="last_name"><?php echo h($row['last_name']); ?></td>
			<td class="first_name"><?php echo h($row['first_name']); ?></td>
			<td class="groupName"><?php echo h($row['groupName']); ?></td>
			<td class="date">
				<?php 
					if($row['date']!=NULL){
						echo $row['date'];
					}else{
						echo "未定";
					}
				?>
			</td>
			<td class="finishFlag">
				<?php
				if($row['finishFlag']==0){
					echo "未完了";
				}else{	//memo ここで中止も追加する？
					echo "完了";
				}
				?>
			</td>
			<td class="edit">
				<?php if($row['finishFlag']==0): ?>
				<form action="editPromotion.php" method="post">
					<input type="hidden" name="orderID" value="<?=$row['orderID']?>">
					<input type="hidden" name="personID" value="<?=$row['personID']?>">
					<input type="submit" class="btn btn-link btn-sm" value="編集">
				</form>
				<?php //memo 編集の中に中止も含める？ ?>
				<?php endif; ?>
			</td>
			<td class="done">
			<?php if($row['finishFlag']==0): ?>
				<form action="donePromotion.php" method="post">
					<input type="hidden" name="orderID" value="<?=$row['orderID']?>">
					<input type="hidden" name="personID" value="<?=$row['personID']?>">
					<input type="submit" class="btn btn-link btn-sm" value="完了報告をする">
				</form>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>