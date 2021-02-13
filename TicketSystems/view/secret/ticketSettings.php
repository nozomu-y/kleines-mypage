<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSecretSession();

  require_once(ROOT.'/controller/settingProcessController.php');
  assignSettingProcess($_POST['submit']);

  require_once(ROOT.'/view/header.php');
  getHeader("チケット設定","secret");
?>
<h2>チケット設定</h2>
<br>
<form action="permissionSettings.php" method="post">
<p>チケットの全ての合計枚数を入力してください</p>
<input class="form-control" type="text" name="sum" value="1500">
<br>
<p>チケット種別と枚数の初期値を入力してください</p>
  <div class="form-row">
    <div class="form-group col-4">
      <p class="componentsTitle">チケット種別名称</p>
    </div>
    <div class="form-group col-4">
      <p class="componentsTitle">初期枚数</p>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-4">
      <input class="form-control" type="text" name="ticketType[0]" value="預かり用回収済み" required>
    </div>
    <div class="form-group col-4">
      <input class="form-control" type="text" name="ticketTypeAmount[0]" value="0" required>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-4">
      <input class="form-control" type="text" name="ticketType[1]" value="団員所持" required>
    </div>
    <div class="form-group col-4">
      <input class="form-control" type="text" name="ticketTypeAmount[1]" value="0" required>
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-4">
      <input class="form-control" type="text" name="ticketType[2]" value="団員販売済(情宣含む)" required>
    </div>
    <div class="form-group col-4">
      <input class="form-control" type="text" name="ticketTypeAmount[2]" value="0" required>
    </div>
  </div>
  <div class="form-block" id="form-block[3]">
    <div class="form-row">
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketType[3]" value="CNプレイガイド委託" required>
      </div>
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketTypeAmount[3]" required>
      </div>
      <div class="form-group col-4">
        <p><span class="remove-button">× 削除する</span></p>
      </div>
    </div>
  </div>
  <div class="form-block" id="form-block[4]">
    <div class="form-row">
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketType[4]" value="チケット交換" required>
      </div>
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketTypeAmount[4]" required>
      </div>
      <div class="form-group col-4">
        <p><span class="remove-button">× 削除する</span></p>
      </div>
    </div>
  </div>
  <div class="form-block" id="form-block[5]">
    <div class="form-row">
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketType[5]" value="OVチケット用にキープ" required>
      </div>
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketTypeAmount[5]" required>
      </div>
      <div class="form-group col-4">
        <p><span class="remove-button">× 削除する</span></p>
      </div>
    </div>
  </div>
  <div class="form-block" id="form-block[6]">
    <div class="form-row">
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketType[6]" value="招待チケット用にキープ" required>
      </div>
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketTypeAmount[6]" required>
      </div>
      <div class="form-group col-4">
        <p><span class="remove-button">× 削除する</span></p>
      </div>
    </div>
  </div>
  <div class="form-block" id="form-block[7]">
    <div class="form-row">
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketType[7]" value="当日券用にキープ" required>
      </div>
      <div class="form-group col-4">
        <input class="form-control" type="text" name="ticketTypeAmount[7]" required>
      </div>
      <div class="form-group col-4">
        <p><span class="remove-button">× 削除する</span></p>
      </div>
    </div>
  </div>
  <div class="form-row">
    <p><span class="add-button">+ 追加する</span></p>
  </div>
  <br>
  <div class="form-row">
    <input type="hidden" name="submit" value="ticket"> 
    <button class="btn btn-primary" type="submit">登録する</button>
  </div>
</form>
<br>
<?php
/**
 * フォームチェックのjs
 * form-block-remaddのjs
 * 
 */

?>

<?php
	require_once(ROOT.'/view/footer.php');
	
?>