<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();

  //orderを受け取り、確認画面に表示する
  //受け取り
  $personID = h($_SESSION['mypage_personID']);
  $orderID = h($_POST['orderID']);
  $lname = h($_POST['lname']);
  $fname = h($_POST['fname']);
  $lnameKana = h($_POST['lnameKana']);
  $fnameKana = h($_POST['fnameKana']);
  $price = h($_POST['price']);
  $amount = h($_POST['amount']);
?>
<?php
	require_once(ROOT.'/view/header.php');
	getHeader("内容確認","everyone");
?>
        <div class="row">
          <div class="col-4">
            <p>氏名</p>
          </div>
          <div class="col-8">
            <p><?php echo $lname." ".$fname;?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <p>氏名(カナ)</p>
          </div>
          <div class="col-8">
            <p><?php echo $lnameKana." ".$fnameKana; ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <p>枚数</p>
          </div>
          <div class="col-8">
            <p><?php echo $amount; ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <p>金額</p>
          </div>
          <div class="col-8">
            <p><?php echo $price; ?></p>
          </div>
        </div>
<br>
<p>この内容で登録してもよろしいですか？</p>
<?php
  $input = <<< IN
  <input type="hidden" name="lname" value="$lname">
  <input type="hidden" name="fname" value="$fname">
  <input type="hidden" name="lnameKana" value="$lnameKana">
  <input type="hidden" name="fnameKana" value="$fnameKana">
  <input type="hidden" name="price" value="$price">
  <input type="hidden" name="personID" value="$personID">
  <input type="hidden" name="amount" value="$amount">
  <input type="hidden" name="orderID" value="$orderID">
  IN;
?>
<div class="row">
  <div class="col-4 col-sm-3">
    <form action="" method="post">
      <?=$input?>
      <button type="submit" class="btn btn-primary btn-block" width="50px">登録する</button>
    </form>
  </div>
  <div class="col-4 col-sm-3">
    <form action="changeReserve.php" method="post">
      <?=$input?>
      <button type="submit" class="btn btn-secondary btn-block">訂正する</button>
    </form>
  </div>
</div>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>