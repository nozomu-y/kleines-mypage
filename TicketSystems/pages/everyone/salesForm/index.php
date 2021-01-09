<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);
  $pageTitle = "チケット販売報告フォーム";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';

  //一度だけ処理を行う
  if(isset($_POST['process']) && strcmp($_POST['process'], "submit")==0){
    require_once __DIR__.'/sold.php';
    $_SESSION['tp_status'] = "succeed-sold";
    header("Location: ".$_SERVER['PHP_SELF']); //更新対策
    exit();
  }

?>
<p class="tx">チケットを売り上げた情報を入力するフォームです</p>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" class="needs-validation" novalidate>
  <div class="form-group">
    <p class="tx">売った合計枚数を入力してください</p>
    <input type="text" class="form-text js-form-item js-valid-amount" name="amount" id="amount" placeholder="枚数を入力してください" required>
    <div class="required-feedback">枚数を入力してください</div>
    <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
    <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
  </div>
  <div class="form-group">
    <p class="tx">チケット預かりを利用しますか？</p>
    <input type="checkbox" name="reserve" id="reserve" value="はい">
		<label class="tx" for="reserve">はい</label>
  </div>
  <div class="form-block" id="form-block[0]" style="display:none;">
    <p class="tx" style="font-weight:bold;">お客様1グループ分の情報を入力してください</p>
    <div class="form-group">
      <p class="tx">姓</p>
      <input class="form-text js-form-item js-valid-name" type="text" name="lname-guest[0]" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <p class="tx">名</p>
      <input class="form-text js-form-item js-valid-name" type="text" name="fname-guest[0]" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <div class="form-group">
      <p class="tx">姓(カナ)</p>
      <input class="form-text js-form-item js-valid-kana" type="text" name="lname-kana-guest[0]" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="format-feedback">全角カナで入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <p class="tx">名(カナ)</p>
      <input class="form-text js-form-item js-valid-kana" type="text" name="fname-kana-guest[0]" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="format-feedback">全角カナで入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <div class="form-group">
      <p class="tx">利用枚数</p>
      <input class="form-text js-form-item js-valid-amount js-valid-positive" type="text" name="amount-guest[0]" required>
      <div class="required-feedback">枚数を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。</div>
    </div>
    <div class="form-group">
      <p class="tx">金額</p>
      <input class="form-text js-form-item js-valid-price" type="text" name="price-guest[0]" required>
      <div class="required-feedback">金額を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。</div>
    </div>
    <button class="btn btn-success js-fb-add" type="button" formNoValidate>+ グループを追加</button>
    <button class="btn btn-danger js-fb-remove" style="display:none;">× グループを削除</button>
  </div>
  <div class="form-group">
    <input type="hidden" name="process" value="submit">
  </div>
  <button class="btn btn-primary js-modal-open js-form-confirm" data-target="confirmModal">入力確認</button>
  <div class="modal js-modal" id="confirmModal">
    <div class="modal-bg js-modal-close"></div>
    <div class="modal-content">
      <div class="modal-header"><div class="modal-title">入力確認</div>
        <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
      </div>
      <div class="modal-main">
        <p class="tx">この内容で送信してもよろしいですか？</p>
        <div class="js-item-list"></div>
      </div>
      <div class="modal-footer">
        <div class="modal-left">
          <button class="btn btn-secondary js-modal-close">戻る</button>
        </div>
        <div class="modal-right">
          <button class="btn btn-primary" type="submit">送信</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- import js files-->
<script src="salesForm.js"></script>
<script src="<?=TP_SERVER?>/include/js/form-block-removable.js"></script>
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>