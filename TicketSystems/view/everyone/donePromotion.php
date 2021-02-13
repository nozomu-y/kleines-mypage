<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	$mysqli = dbconnect();
	//memo ページ読み込み時に、セッション情報とorderIDからのselect結果を比較し、違う人はアクセスできないようにする
	//memo もしくは、promotionListのボタンクリック時にscriptでユーザーチェック

	/** 完了報告アルゴリズム
	 * (orderIDでtp_Promotionsから情宣のデータを得られる)
	 * actualAmountを入力してもらう
	 * 	その時に、一緒に行った人を追加する(ここは性善説で大丈夫か？)
	 * 		↑マイページと連動すれば、報告が送られるようにすれば大丈夫かな
	 * 	動的な追加→soldTicketのjquery参考に
	 * tp_Orders.amount - 各自の販売数の合計をfinishPromotionのorderに入れる(orderType 7)
	 * 	↑チケットが足りなくなって自分の分から売った場合は？()
	 * 	↑情宣用チケットと各自のチケットで欄を分けるが、初期設定とかでも反映させたい
	 * 同時に、各自のチケット販売分を反映
	 * tp_Promotions.finishFlagを1にupdate,actualAmountに合計数を反映
	*/

	//personIDが違った場合(代表者じゃなかった場合)、拒否
	$personID = h($_POST['personID']);
	if($personID!=$_SESSION['mypage_personID']){
		//弾く
		$_SESSION['tp_status'] = "notRepresent";
		header("Location: ".SERVER."/view/everyone/promotionList.php");
		exit();
	}

	$orderID = h($_POST['orderID']);
	//orderのamountを取得
	$stmt = $mysqli->prepare("SELECT tp_Orders.amount FROM tp_Promotions INNER JOIN tp_Orders USING(orderID) WHERE orderID = ?");
	$stmt->bind_param('i',$orderID);
	$stmt->execute();
	$stmt->bind_result($am);
	$result = $stmt->fetch();
  if($result == NULL){
    echo "<!--orderID is invald-->";
    exit;
	}
	$amount = $am;
	$stmt->close();

	//最高学年、最低学年を取得(2000年問題)
	$stmt = $mysqli->prepare("SELECT max(grade),min(grade) FROM members");
	$stmt->execute();
	$stmt->bind_result($Ma,$mi);
	$result = $stmt->fetch();
	if($result == NULL){
    echo "<!--no grade data in members-->";
    exit();
	}
	$newestGrade = $Ma;	//1年生
	$oldestGrade = $mi;	//最上級生
	$stmt->close();
	dbclose($mysqli);
	
	//first_name,last_nameを取得
	$grade = $_SESSION['grade'];
	$part = $_SESSION['part'];
	$firstName = $_SESSION['fname'];
	$lastName = $_SESSION['lname'];

	require_once(ROOT.'/view/header.php');
	getHeader("情宣終了報告","everyone");
?>
<h2>情宣終了報告</h2>
<br>
<form action="finishOrder.php" method="post" class="needs-validation" novalidate>
	<input type="hidden" name="orderID" value="<?=$orderID?>">
	<input type="hidden" name="personID" value="<?=$personID?>">
	<input type="hidden" name="amount_prticket" value="<?=$amount?>">
	<input type="hidden" name="orderType" value="7">
	<div class="form-row">
		<div class="form-group col-3 col-md-3">
			<p class="componentsTitle">学年</p>
			<input type="text" class="form-control" name="represent_grade" value="<?=$grade?>" readonly>
		</div>
		<div class="form-group col-3 col-md-3">
			<p class="componentsTitle">姓</p>
			<input type="text" class="form-control" name="represent_part" value="<?=$part?>" readonly>
		</div>
		<div class="form-group col-3 col-md-3">
			<p class="componentsTitle">姓</p>
			<input type="text" class="form-control" name="represent_lname" value="<?=$lastName?>" readonly>
		</div>
		<div class="form-group col-3 col-md-3">
			<p class="componentsTitle">名</p>
			<input type="text" class="form-control" name="represent_fname" value="<?=$firstName?>" readonly>
		</div>
	</div>
	<div class="form-row">
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">情宣用チケットから売った枚数</p>
			<input type="text" class="form-control" name="represent_amount_pr" required>
			<div class="invalid-feedback">入力してください</div>
		</div>
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">個人のチケットから売った枚数</p>
			<input type="text" class="form-control" name="represent_amount_self" required>
			<div class="invalid-feedback">入力してください</div>
		</div>
	</div>
	<div class="form-group">
		<p class="componentsTitle">他にも情宣に同行した人がいますか？</p>
		<div class="custom-control custom-checkbox">
		<input type="checkbox" class="custom-control-input" name="accompany" id="accompany" value="1">
			<label class="custom-control-label" for="accompany">はい</label>
		</div>
	</div>
	<div class="form-block" id="form-block[0]" style="display:none">
		<div class="form-row">
			<div class="form-group col-3 col-md-3">
				<p class="componentsTitle">学年</p>
				<select class="form-control" id="member_grade[0]" name="member_grade[0]">
					<option value="">未選択</option>
					<?php for($grd=$newestGrade;$grd>=$oldestGrade;$grd--): ?>
					<option value="<?=$grd?>"><?=$grd?></option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="form-group col-3 col-md-3">
				<p class="componentsTitle">パート</p>
				<select class="form-control" id="member_part[0]" name="member_part[0]">
					<option value="">未選択</option>
					<option value="S">Sop</option>
					<option value="A">Alt</option>
					<option value="T">Ten</option>
					<option value="B">Bas</option>
				</select>
			</div>
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">名前</p>
				<select class="form-control" id="member_name[0]" name="member_name[0]">
					<option value="">未選択</option>
				</select>
				<?php //memo 未選択でも提出できてしまうので、バリデーションする ?>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">情宣用チケットから売った枚数</p>
				<input type="text" class="form-control" id="member_amount_pr[0]" name="member_amount_pr[0]" required>
				<div class="invalid-feedback">入力してください</div>
			</div>
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">個人のチケットから売った枚数</p>
				<input type="text" class="form-control" id="member_amount_self[0]" name="member_amount_self[0]" required>
				<div class="invalid-feedback">入力してください</div>
			</div>
		</div>
		<div class="form-row">
			<div class="addrem-button col-5 col-md-5 text-nowrap">
				<p><span class="add-button">+ メンバーを追加</span></p>
			</div>
			<div class="col-1 col-md-2"></div>
			<div class="addrem-button col-6 col-md-5 text-nowrap">
				<p><span class="remove-button" style="display:none">× このメンバーを削除</span></p>
			</div>
		</div>
	</div>
	<button type="button" id="btn-confirm" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">入力確認</button>
  <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="label1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="label1">入力確認</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>この内容で送信しますか？</p><br>
          <div class="row">
            <div class="col-4"><p>送信者</p></div>
            <div class="col-8"><p>
              <?=$_SESSION['grade'].$_SESSION['part']." ".$_SESSION['lname'].$_SESSION['fname']?>
            </p></div>
          </div>
          <div class="row">
            <div class="col-4"><p>オーダー種別</p></div>
            <div class="col-8"><p>情宣終了報告</p></div>
          </div>
          <div class="row">
            <div class="col-4"><p>渉外のチケットから売った枚数</p></div>
            <div class="col-8"><p>
              ??
              <!--jsで内容を取得してここに表示する-->
            </p></div>
          </div>
					<div class="row">
            <div class="col-4"><p>自分のチケットから売った枚数</p></div>
            <div class="col-8"><p>
              ??
              <!--jsで内容を取得してここに表示する-->
            </p></div>
          </div>
					<div>
						<!--同伴者情報-->
					</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">修正する</button>
					<button type="submit" class="btn btn-primary">送信する</button>
        </div>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript" src="<?=SERVER?>/view/js/donePromotion.js"></script>
<script type="text/javascript" src="<?=SERVER?>/view/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?=SERVER?>/view/js/validate-confirm.js"></script>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>