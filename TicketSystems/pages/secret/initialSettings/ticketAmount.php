<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  secretFilter(MAX_CHIEF_PERM, $USER->id, $mysqli);

  //一度だけ処理を行う
  if(isset($_POST['process']) && $_POST['process'] === "initTables"){
    require_once __DIR__.'/initTables.php';
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }

  //DBからtp_TicketTotalに既に存在するチケット種別と枚数を取得する
  $q_select = "SELECT * FROM tp_TicketTotal";
  $result = $mysqli->query($q_select);
  $t_types = [];
  if($result==NULL){
    //error 
  }
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $t_types[] = $row;
  }
  $result->free();

  $pageTitle = "チケット枚数設定";
  $applyStyle = "secret";
  require_once TP_ROOT.'/include/header.php';
?>
<?php //TODO: 説明をモーダルやアコーディオン等隠せるものに変更 ?>
<p class="tx">チケットの種別についての設定を行います。</p>
<p class="tx">「チケットの全ての合計枚数」は、ホールに呼べる最大数を入力してください。</p>
<p class="tx">「チケット種別・枚数の初期値」は、それぞれチケットの種類の名前と、それが何枚あるかを入力してください。</p>
<p class="tx">追加ボタンを押すと1つ追加でき、削除ボタンを押すとそのチケット種別は削除されます。</p>
<p class="tx">「渉外所持」は、合計枚数からその他を除いた分の枚数が自動で入力されます。</p><br>
<form action="permission.php" method="post" class="needs-validation">
  <p class="tx">チケットの全ての合計枚数</p>
  <div class="form-group">
    <input class="form-text" type="text" name="sumAmount" value="1500">
  </div>
  <p class="tx" style="font-weight:400;">チケット種別・枚数の初期値</p>
  <?php
    for($i=0; $i<count($t_types); $i++):
  ?>
  <div class="form-block <?php if(!$t_types[$i]["isDefault"]) echo("js-fb-removable");?>" id="form-block[<?=$i?>]">
    <div class="f-container form-group">
      <div class="fb-TicType-name">
        <p class="tx">チケット種別名称</p>
      </div>
      <div class="fb-TicType-amount">
        <p class="tx">初期枚数</p>
      </div>
      <div class="fb-TicType-name">
        <input class="form-text js-form-item" type="text" name="ticketType[<?=$i?>]" 
          value="<?=$t_types[$i]['ticketTypeValue']?>" required 
          <?php if($t_types[$i]["isDefault"]) echo("readonly"); ?>>
        <div class="required-feedback">名前を入力してください</div>
        <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      </div>
      <div class="fb-TicType-amount">
        <input class="form-text js-form-item js-valid-amount" type="text" name="ticketTypeAmount[<?=$i?>]" value="<?=$t_types[$i]["amount"]?>" required 
        <?php if($i==0) echo("readonly"); //渉外所持は、チケット全体枚数 - その他の枚数で算出するためreadonly ?>>
        <div class="required-feedback">枚数を入力してください</div>
        <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
        <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      </div>
    </div>
    <button class="btn btn-danger js-fb-remove" 
    <?php if($t_types[$i]["isDefault"]) echo('style="display:none;"');?> >× 削除する</button>
  </div>
  <?php /*
  <div class="form-block" id="form-block[<?=$i?>]">
    <div class="form-group">
      <input class="form-text js-form-item col-8" type="text" name="ticketType[<?=$i?>]" value="<?=$t_types[$i]['ticketTypeValue']?>" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <input class="form-text js-form-item js-valid-amount　col-4" type="text" name="ticketTypeAmount[<?=$i?>]" value="0" required 
      <?php if($i==0) echo("readonly"); //渉外所持は、チケット全体枚数 - その他の枚数で算出するためreadonly ?>>
      <div class="required-feedback">枚数を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <button class="btn btn-danger js-fb-remove" style="display:none;">× 削除する</button>
  </div>
  */ ?>
  <?php endfor;?>
  <button class="btn btn-success js-fb-add" type="button" formNoValidate>+ 追加する</button><br>
  <input type="hidden" name="process" value="ticket">
  <button class="btn btn-primary js-modal-open js-form-confirm" data-target="confirmModal">入力確認</button>
  <div class="modal js-modal" id="confirmModal">
    <div class="modal-bg js-modal-close"></div>
    <div class="modal-content">
      <div class="modal-header"><div class="modal-title">入力確認</div>
        <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
      </div>
      <div class="modal-main">
        <p class="tx">チケット種別と枚数は以下の通りでよろしいですか？</p><br>
        <div class="js-item-list"></div>
      </div>
      <div class="modal-footer">
        <div class="modal-left">
          <button class="btn btn-secondary js-modal-close">戻る</button>
        </div>
        <div class="modal-right">
          <button class="btn btn-primary" type="submit">はい</button>
        </div>
      </div>
    </div>
  </div>
</form>
<!-- import js files-->
<script src="ticketAmount.js"></script>
<script src="<?=TP_SERVER?>/include/js/form-block-removable.js"></script>
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>