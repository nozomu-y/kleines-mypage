<?php
  //一度だけ処理を行う
  //TODO ブラウザバックの対応
  if($_POST['process']=="initTables"){
    require_once(__DIR__.'/initTables.php');
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }

  //DBからtp_TicketTotalに既に存在するチケット種別と枚数を取得する
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/Common/dbconnect.php');
  $q_select = "SELECT ticketTypeCode, ticketTypeValue, amount FROM tp_TicketTotal";
  $result = $mysqli->query($q_select);
  $default_assign = [];
  if($result==NULL){
    //error 
  }
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $default_assign[] = $row;
  }
  //結果セットを解放
  $result->free();

  //追加で用意する、可変のチケット区分(枚数は0)
  $add_assign = [
    ["ticketTypeValue"=>"CNプレイガイド委託","amount"=>0],
    ["ticketTypeValue"=>"チケット交換","amount"=>0],
    ["ticketTypeValue"=>"OVチケット用にキープ","amount"=>0],
    ["ticketTypeValue"=>"招待チケット用にキープ","amount"=>0],
    ["ticketTypeValue"=>"当日券用にキープ","amount"=>0]
  ];

  //require_once($_SERVER['DOCUMENT_ROOT']."/TicketSystems/kleines-mypage/Common/init_page.php");
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  $pageTitle = "チケット枚数設定";
  $applyStyle = "secret";
  require_once(ROOT.'/include/header.php');
?>
<form action="permission.php" method="post" class="needs-validation">
  <p class="tx">チケットの全ての合計枚数を入力してください</p>
  <div class="form-group">
    <input class="form-text" type="text" name="sumAmount" value="1500">
  </div>
  <p class="tx">チケット種別と枚数の初期値を入力してください</p><br>
  <div class="form-group">
    <p class="tx col-8">チケット種別名称</p>
    <p class="tx col-4">初期枚数</p>
  </div>
  <?php
    /*
      渉外所持を表示し、他の個数が変わるたびに数を更新する(readonly)
      ticketType[index] にして、index+1をIDにしてtp_ticketTotalに挿入
      最初にtp_ticketTotalに既に存在しているものはあらかじめ取得して、削除不可な状態で表示(名前は変更可能にする？)
      その後に、表示用の可変部分をブロック形式で表示する(ブロック形式：削除可能な形態)
      FIXME: 追加ボタンを押したときにもrequiredのチェックが入る(動作は普通にできる)
      ↑困ってはないので、優先順位めちゃくちゃ低くて良い
      TODO: 合計枚数のバリデーションと、渉外所持の自動調節を行うticketAmount.jsを作成する

    */
  ?>
  <?php
    for($i=0; $i<count($default_assign); $i++):
  ?>
  <div class="form-block" id="form-block[<?=$i?>]">
    <div class="form-group">
      <input class="form-text js-form-item col-8" type="text" name="ticketType[<?=$i?>]" value="<?=$default_assign[$i]['ticketTypeValue']?>" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <input class="form-text js-form-item js-valid-amount　col-4" type="text" name="ticketTypeAmount[<?=$i?>]" value="0" required 
      <?php if($i===0) echo("readonly"); //渉外所持は、チケット全体枚数 - その他の枚数で算出するため ?>>
      <div class="required-feedback">枚数を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <button class="btn btn-danger js-fb-remove" style="display:none;">× 削除する</button>
  </div>
  <?php
    endfor;
    for($i_add=0; $i_add<count($add_assign); $i_add++ ):
    $i_all = $i_add + count($default_assign);
  ?>
  <div class="form-block js-fb-removable" id="form-block[<?=$i_all?>]">
    <div class="form-group">
      <input class="form-text js-form-item col-8" type="text" name="ticketType[<?=$i_all?>]" value="<?=$add_assign[$i_add]['ticketTypeValue']?>" required>
      <div class="required-feedback">名前を入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <input class="form-text js-form-item js-valid-amount　col-4" type="text" name="ticketTypeAmount[<?=$i_all?>]" required>
      <div class="required-feedback">枚数を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <button class="btn btn-danger js-fb-remove">× 削除する</button>
  </div>
  <?php endfor;?>
  <button class="btn btn-success js-fb-add">+ 追加する</button>
  <input type="hidden" name="process" value="ticket">
  <button class="btn btn-primary js-modal-open js-form-confirm" data-target="#confirmModal">入力確認</button>
  <div class="modal js-modal" id="confirmModal">
    <div class="modal-bg js-modal-close"></div>
    <div class="modal-content">
      <div class="modal-header"><div class="modal-title">入力確認</div>
        <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
      </div>
      <div class="modal-main">
        <p class="tx">チケット種別と枚数は以下の通りでよろしいですか？</p>
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
<script src="<?=SERVER?>/pages/js/form-block-removable.js"></script>
<script src="<?=SERVER?>/pages/js/form-modal.js"></script>
<?php require_once(ROOT.'/include/footer.php'); ?>