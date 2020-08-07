<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	require_once(ROOT.'/view/header.php');
	getHeader("チケット販売報告フォーム","everyone");
?>
<?php 
	//memo 入力値を設定するjsを作りたい………………
?>
<h2>チケット販売報告</h2>
<br>
<form action="finishOrder.php" method="post" class="needs-validation" novalidate>
	<div class="form-row">
		<div class="form-group col-6 col-md-6">
			<p class="componentsTitle">売った枚数</p>
			<input type="text" class="form-control 6 md-6" name="amount_all" id="amount_all" required>
			<div class="invalid-feedback">入力してください</div>
		</div>
	</div>
	<div class="form-group">
		<p class="componentsTitle">チケット預かりを利用しますか？</p>
		<div class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" name="reserve_use" id="reserve_use_true" value="1">
			<label class="custom-control-label" for="reserve_use_true">はい</label>
		</div>
	</div>
	<input type="hidden" name="orderType" value="2">
	<input type="hidden" name="personID" value="<?=h($_SESSION['mypage_personID'])?>">
	<div class="form-block" id="form-block[0]" style="display:none">
		<p class="componentsTitle">お客様の情報を入力してください</p>
		<div class="form-row">
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">姓</p>
				<input type="text" class="form-control 6 md-6" name="guest_lname[0]" id="guest_lname[0]" required>
				<div class="invalid-feedback">入力してください</div>
			</div>
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">名</p>
				<input type="text" class="form-control 6 md-6" name="guest_fname[0]" id="guest_fname[0]" required>
				<div class="invalid-feedback">入力してください</div>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">姓(カナ)</p>
				<input type="text" class="form-control 6 md-6" name="guest_lname_kana[0]" id="guest_lname_kana[0]" required>
				<div class="invalid-feedback">入力してください</div>
			</div>
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">名(カナ)</p>
				<input type="text" class="form-control 6 md-6" name="guest_fname_kana[0]" id="guest_fname_kana[0]" required>
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
				<input type="text" class="form-control 6 md-6" name="guest_amount[0]" id="guest_amount[0]" required>
				<div class="invalid-feedback">入力してください</div>
			</div>
		</div>
		<div class="form-row">
			<div class="form-text text-muted">
				<p class="helptext">代表者に渡す場合はグループの人数を、1人ずつの場合には1を入力してください</p>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-6 col-md-6">
				<p class="componentsTitle">値段(500円単位のみ)</p>
				<input type="text" class="form-control 6 md-6" name="price[0]" id="price[0]" required>
				<div class="invalid-feedback">入力してください</div>
			</div>
		</div>
		<div class="form-row">
			<div class="addrem-button col-5 col-md-5 text-nowrap">
				<p><span class="add-button">+ グループを追加</span></p>
			</div>
			<div class="col-1 col-md-2"></div>
			<div class="addrem-button col-6 col-md-5 text-nowrap">
				<p><span class="remove-button" style="display:none">× このグループを削除</span></p>
			</div>
		</div>
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
            <div class="col-8"><p>チケット販売報告</p></div>
          </div>
          <div class="row">
            <div class="col-4"><p>枚数</p></div>
            <div class="col-8"><p>
              ??
              <!--jsで内容を取得してここに表示する-->
            </p></div>
          </div>
					<div class="row">
            <div class="col-4"><p>チケット預かり利用</p></div>
            <div class="col-8"><p>
              ??
              <!--jsで内容を取得してここに表示する-->
            </p></div>
          </div>
					<div>
						<!--あずかり情報-->
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
<script type="text/javascript" src="<?=SERVER?>/view/js/soldTicket.js"></script>
<script type="text/javascript" src="<?=SERVER?>/view/js/formValidation.js"></script>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>
	