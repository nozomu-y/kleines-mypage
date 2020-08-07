<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  
  //memo このページを使わずに、モーダルでできるようにする
  //memo この画面で団員情報を取得しているので、元のページで取得するようにする

  //orderを受け取り、確認画面に表示する
  //受け取り
  $personID = h($_SESSION['mypage_personID']);
  $grade = h($_SESSION['grade']);
  $part = h($_SESSION['part']);
  $lname = h($_SESSION['lname']);
  $fname = h($_SESSION['fname']);
  $amount_all = h($_POST['amount_all']);
  $orderType = h($_POST['orderType']);
  $reserve_use = h($_POST['reserve_use']);
  if($reserve_use == 1){
    //預かり情報の配列を受け取る
    $guest_lname = $_POST['guest_lname'];
    $guest_fname = $_POST['guest_fname'];
    $guest_lname_kana = $_POST['guest_lname_kana'];
    $guest_fname_kana = $_POST['guest_fname_kana'];
    $guest_amount = $_POST['guest_amount'];
    $price = $_POST['price'];
  }
?>
<?php
	require_once(ROOT.'/view/header.php');
	getHeader("内容確認","everyone");
?>
<h2>内容確認</h2>
<div class="row">
  <div class="col-4">
    <p>学年</p>
  </div>
  <div class="col-8">
    <p><?php echo $grade; ?></p>
  </div>
</div>
<div class="row">
  <div class="col-4">
    <p>パート</p>
  </div>
  <div class="col-8">
    <p><?php echo $part; ?></p>
  </div>
</div>
<div class="row">
  <div class="col-4">
    <p>名前</p>
  </div>
  <div class="col-8">
    <p><?php echo $lname;echo " "; echo $fname; ?></p>
  </div>
</div>
<div class="row">
  <div class="col-4">
    <p>枚数</p>
  </div>
  <div class="col-8">
    <p><?php echo $amount_all; ?></p>
  </div>
</div>
<div class="row">
  <div class="col-4">
    <p>属性</p>
  </div>
  <div class="col-8">
    <p><?php echo $orderType;?></p>
  </div>
</div>
<div class="row">
  <div class="col-4">
    <p>チケット預かりの利用</p>
  </div>
  <div class="col-8">
    <p><?php 
    if($reserve_use==1){
      echo "あり";
    }else{
      echo "なし";
    }
    ?></p>
  </div>
</div>
<br>
<?php
  if($reserve_use==1):
    //預かりの分だけ(count()の分だけ)内容を表示
    for($i=0;$i<count($guest_lname);$i++):
      $num = $i + 1;
      ?>
      <div class="form-block">
        <p class="componentsTitle">グループ<?=$num?></p>
        <div class="row">
          <div class="col-4">
            <p>氏名</p>
          </div>
          <div class="col-8">
            <p><?php echo h($guest_lname[$i])." ".h($guest_fname[$i]);?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <p>氏名(カナ)</p>
          </div>
          <div class="col-8">
            <p><?php echo h($guest_lname_kana[$i])." ".h($guest_fname_kana[$i]); ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <p>枚数</p>
          </div>
          <div class="col-8">
            <p><?php echo h($guest_amount[$i]); ?></p>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <p>金額</p>
          </div>
          <div class="col-8">
            <p><?php echo h($price[$i]); ?></p>
          </div>
        </div>
      </div>
<?php
    endfor;
  endif;
?>
<p>この内容で登録してもよろしいですか？</p>
<?php
  $input = <<<INPUT
  <input type="hidden" name="personID" value="$personID">
  <input type="hidden" name="grade" value="$grade">
  <input type="hidden" name="part" value="$part">
  <input type="hidden" name="last_name" value="$lname">
  <input type="hidden" name="first_name" value="$fname">
  <input type="hidden" name="amount_all" value="$amount_all">
  <input type="hidden" name="orderType" value="$orderType">
  <input type="hidden" name="reserve_use" value="$reserve_use">
  INPUT;
?>
<div class="row">
  <div class="col-4 col-sm-3">
    <form action="finishOrder.php" method="post">
      <?php echo $input;
        if($reserve_use == 1){
          for($i=0;$i<count($guest_lname);$i++){
            echo "<input type=\"hidden\" name=\"guest_lname[$i]\" value=\"".h($guest_lname[$i])."\">";
            echo "<input type=\"hidden\" name=\"guest_fname[$i]\" value=\"".h($guest_fname[$i])."\">";
            echo "<input type=\"hidden\" name=\"guest_lname_kana[$i]\" value=\"".h($guest_lname_kana[$i])."\">";
            echo "<input type=\"hidden\" name=\"guest_fname_kana[$i]\" value=\"".h($guest_fname_kana[$i])."\">";
            echo "<input type=\"hidden\" name=\"guest_amount[$i]\" value=\"".h($guest_amount[$i])."\">";
            echo "<input type=\"hidden\" name=\"price[$i]\" value=\"".h($price[$i])."\">";
          }
          echo "<input type=\"hidden\" name=\"num_reserve\" value=\"".count($guest_lname)."\">";
        }
      ?>
      <button type="submit" class="btn btn-primary btn-block" width="50px">登録する</button>
    </form>
  </div>
  <div class="col-4 col-sm-3">
    <?php
    if($orderType==2){
      echo "<form action=\"soldTicket.php\" method=\"post\">";
    }else{
      echo "<form action=\"failureOrder.php\" method=\"post\">";
    }
      echo $input;
      if($reserve_use == 1){
        for($i=0;$i<count($guest_lname);$i++){
          echo "<input type=\"hidden\" name=\"guest_lname[$i]\" value=\"".h($guest_lname[$i])."\">";
          echo "<input type=\"hidden\" name=\"guest_fname[$i]\" value=\"".h($guest_fname[$i])."\">";
          echo "<input type=\"hidden\" name=\"guest_lname_kana[$i]\" value=\"".h($guest_lname_kana[$i])."\">";
          echo "<input type=\"hidden\" name=\"guest_fname_kana[$i]\" value=\"".h($guest_fname_kana[$i])."\">";
          echo "<input type=\"hidden\" name=\"guest_amount[$i]\" value=\"".h($guest_amount[$i])."\">";
          echo "<input type=\"hidden\" name=\"price[$i]\" value=\"".h($price[$i])."\">";
        }
        echo "<input type=\"hidden\" name=\"num_reserve\" value=\"".count($guest_lname)."\">";
      }
    ?>
      <button type="submit" class="btn btn-secondary btn-block">訂正する</button>
    </form>
  </div>
</div>
<br>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>