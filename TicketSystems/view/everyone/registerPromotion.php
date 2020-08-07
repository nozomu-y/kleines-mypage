<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	require_once(ROOT.'/view/header.php');
	getHeader("情宣登録","everyone");
?>
<h2>情宣登録</h2>
<br>
<form action="finishOrder.php" method="post" class="needs-validation" novalidate>
	<div class="form-group">
		<p class="componentsTitle">訪問団体名</p>
		<input type="text" class="form-control 6 md-6" name="groupName" id="groupName" rquired>
		<div class="invalid-feedback">
			入力してください
		</div>
	</div>
	<div class="form-group">
		<?php //memo : 情宣用チケットを分けるか、各自のチケットでやってもらうかを、初期設定とかの時に決めたい ?>
		<p class="componentsTitle">チケット(追加)希望枚数</p>
		<input type="text" class="form-control 6 md-6" name="amount" id="amount" required>
		<div class="invalid-feedback">
			入力してください
		</div>
	</div>
	<div class="form-row">
		<div class="form-text text-muted">
			<p class="helptext">枚数に見当がつかない場合も、後から変更できるので、とりあえずの数を入力してください</p>
		</div>
	</div>
	<div class="form-group">
		<p class="componentsTitle">日時はすでに決まっていますか？</p>
		<div class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" name="date_determined" id="date_determined" value="1">
			<label class="custom-control-label" for="date_determined">はい</label>
		</div>
	</div>
	<div class="form-group">
		<p class="componentsTitle">情宣日程</p>
	</div>
	<div class="form-row" id="date_select">
		<div class="form-group col-4 col-md-4">
			<label for="year">年</label>
			<select class="form-control" id="year" name="year">
				<?php
				for($i=0;$i<2;$i++){	//今年中または年明けに演奏会を行う
					$year = date('Y') + $i;
					echo "<option value='$year'>$year</option>";
				}
				?>
			</select>
		</div>
		<div class="form-group col-4 col-md-4">
			<label for="month">月</label>
			<select class="form-control" id="month" name="month">
				<?php
				for($i=1;$i<=12;$i++){
					$month = $i;
					echo "<option value='$month'>$month</option>";
				}
				?>
			</select>
		</div>
		<div class="form-group col-4 col-md-4">
			<label for="day">日</label>
			<select class="form-control" id="day" name="day">
				<?php
				for($i=1;$i<=31;$i++){
					$day = $i;
					echo "<option value='$day'>$day</option>";
				}
				//memo js使ってバリデーション、POSTで入力済みの値受けとり
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<input type="hidden" name="orderType" value="4">
		<input type="hidden" name="personID" value="<?=h($_SESSION['mypage_personID'])?>">
	</div>
	<button type="button" id="btn-confirm" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
    入力確認
    <!--ここでformValidationが発動するようにする-->
  </button>
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
            <div class="col-8"><p>情宣登録</p></div>
          </div>
          <div class="row">
            <div class="col-4"><p>枚数</p></div>
            <div class="col-8"><p>
              ??
              <!--jsで内容を取得してここに表示する-->
            </p></div>
          </div>
					<div class="row">
            <div class="col-4"><p>日時</p></div>
            <div class="col-8"><p>
              未定または決定
              <!--jsで内容を取得してここに表示する-->
            </p></div>
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
<script type="text/javascript" src="<?=SERVER?>/view/js/registerPromotion.js"></script>
<script type="text/javascript" src="<?=SERVER?>/view/js/formValidation.js"></script>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>