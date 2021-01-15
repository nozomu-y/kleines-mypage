<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);

  //一度だけ処理を行う
  if(isset($_POST['process']) && strcmp($_POST['process'], "edit-submit")==0){
    require_once __DIR__.'/editReserve.php';
    $_SESSION['tp_status'] = "edit-reserve";
    header("Location: index.php"); //更新対策
    exit();
  }else if(isset($_POST['process']) && strcmp($_POST['process'], "delete-submit")==0){
    require_once __DIR__.'/deleteReserve.php';
    $_SESSION['tp_status'] = "delete-reserve";
    header("Location: index.php"); //更新対策
    exit();
  }

  $orderID = $_GET['orderID'];
  //IDのバリデーション

  //情報を取得
  $stmt_reserves = $mysqli->prepare("SELECT lastName, firstName, lastNameKana, firstNameKana, amount, price FROM tp_Reserves 
  INNER JOIN tp_Orders USING(orderID) INNER JOIN members USING(id) WHERE orderID = ?");
  $stmt_reserves->bind_param('i', $orderID);
  $stmt_reserves->execute();
  $stmt_reserves->bind_result($lname, $fname, $lnameKana, $fnameKana, $amount, $price);
  if(!$stmt_reserves->fetch()){
    echo($mysqli->error);
    exit();
  }

  $pageTitle = "預かり編集";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>" class="needs-validation" novalidate>
  <div class="form-block">
    <p class="tx" style="font-weight:bold;">お客様1グループ分の情報を入力してください</p>
    <div class="form-group">
      <p class="tx">姓</p>
      <input class="form-text js-form-item js-valid-name" type="text" name="lname" value="<?=$lname?>" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <p class="tx">名</p>
      <input class="form-text js-form-item js-valid-name" type="text" name="fname" value="<?=$fname?>" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <div class="form-group">
      <p class="tx">姓(カナ)</p>
      <input class="form-text js-form-item js-valid-kana" type="text" name="lname-kana" value="<?=$lnameKana?>" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="format-feedback">全角カナで入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <p class="tx">名(カナ)</p>
      <input class="form-text js-form-item js-valid-kana" type="text" name="fname-kana" value="<?=$fnameKana?>" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="format-feedback">全角カナで入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <?php /*
    <div class="form-group">
      <p class="tx">利用枚数</p>
      <input class="form-text js-form-item js-valid-amount js-valid-positive" type="text" name="amount" value="<?=$amount?>" required>
      <div class="required-feedback">枚数を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。</div>
    </div>
    */ ?>
    <div class="form-group">
      <p class="tx">金額</p>
      <input class="form-text js-form-item js-valid-price" type="text" name="price" value="<?=$price?>" required>
      <div class="required-feedback">金額を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。</div>
    </div>
  </div>
  <input type="hidden" name="orderID" value="<?=$orderID?>">
  <input type="hidden" name="process" value="edit-submit">
  <button type="button" class="btn btn-primary js-form-confirm" data-target="confirmEdit">入力確認</button>
  <div class="modal js-modal" id="confirmEdit">
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
<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
  <input type="hidden" name="orderID" value="<?=$orderID?>">
  <input type="hidden" name="process" value="delete-submit">
  <button class="btn btn-danger js-modal-open" type="button" data-target="confirmDelete">この預かり情報を削除</button>
  <div class="modal js-modal" id="confirmDelete">
    <div class="modal-bg js-modal-close"></div>
    <div class="modal-content">
      <div class="modal-header"><div class="modal-title">入力確認</div>
        <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
      </div>
      <div class="modal-main">
        <p class="tx">この預かり情報を削除してよろしいですか？</p>
      </div>
      <div class="modal-footer">
        <div class="modal-left">
          <button class="btn btn-secondary js-modal-close">戻る</button>
        </div>
        <div class="modal-right">
          <button class="btn btn-danger" type="submit">削除する</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- import js files-->
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<script src="<?=TP_SERVER?>/include/js/modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>