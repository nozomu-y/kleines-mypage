<?php
  //ini_set("display_errors",1);
  //error_reporting(E_ALL);
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  accessFilter();
  $mysqli = dbconnect();

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
  
  /** 
   * POSTの中身が入っていたら、requestを0にして、その分haveを増やし、finishFlagを1にする、finishTimeをセットする
   * その後、POSTの中身を空にする?(更新対策)
  */
  $orderType = h($_GET['ticketType']);
  if($_POST['submit']=="submit"){
    require_once(ROOT.'/model/ticketHandler.php');
    header("Location:".$_SERVER['PHP_SELF']."?ticketType=$orderType");
    exit();
  }
  echo $status;

  require_once(ROOT.'/view/header.php');
  switch($orderType){
    case 1: //giveTicket
      getHeader("チケット配布","private");
      echo "<h2>チケット配布</h2>";
      echo "<p>チケットを配布する内容を選んでください</p>";
      break;
    case 3: //reserveReturnTicket
      getHeader("返却チケット受け取り","private");
      echo "<h2>返却チケット受け取り</h2>";
      echo "<p>チケットを受け取る内容を選んでください</p>";
      break;
    case 5: //reserveReservedTicket
      getHeader("預かり用チケット受け取り","private");
      echo "<h2>預かり用チケット受け取り</h2>";
      echo "<p>チケットを預かる内容を選んでください</p>";
      break;
    case 4: //wantPromotion
      getHeader("情宣用チケット配布","private");
      echo "<h2>情宣用チケット配布</h2>";
      echo "<p>チケットを渡す内容を選んでください</p>";
      break;
    case 7: //finishPromotion
      getHeader("情宣用チケット回収","private");
      echo "<h2>情宣用チケット回収</h2>";
      echo "<p>チケットを回収する内容を選んでください</p>";
      break;
    default:
      $_SESSION['tp_status'] = "invalidPage";
      header("Location: ".SERVER."/view/private/index.php?status=$orderType");
      exit();
      break;
  }
  
  /******
   * requestが1以上のものをリストで表示
   * チェックボックスをクリックして、更新ボタンを押すと、
   *   requestを0にして、その数をhaveに増やし、finishFlagを1にし、finishTimeをセットする
   */
?>
<br>
<h3>フィルタリング</h3>
<p>パート</p>
<div class="row">
  <button class="btn btn-secondary" name="filter-part-clear" id="filter-part-clear">Clear</button>
  <div class="btn-group btn-group-toggle" data-toggle="buttons">
    <label class="btn btn-secondary">
      <input type="checkbox" name="filter-part[]" value="S" autocomplete="off">Sop
    </label>
    <label class="btn btn-secondary">
      <input type="checkbox" name="filter-part[]" value="A" autocomplete="off">Alt
    </label>
    <label class="btn btn-secondary">
      <input type="checkbox" name="filter-part[]" value="T" autocomplete="off">Ten
    </label>
    <label class="btn btn-secondary">
      <input type="checkbox" name="filter-part[]" value="B" autocomplete="off">Bas
    </label>
  </div>
</div>
<p>学年</p>
<div class="row">
  <button class="btn btn-secondary" name="filter-grade-clear" id="filter-grade-clear">Clear</button>
  <div class="btn-group btn-group-toggle" data-toggle="buttons">
    <?php for($grd=$newestGrade;$grd>=$oldestGrade;$grd--): ?>
    <label class="btn btn-secondary">
      <input type="checkbox" name="filter-grade[]" value="<?=$grd?>" autocomplete="off"><?=$grd?>
    </label>
    <?php endfor; ?>
  </div>
</div>
<br>
<form action="<?=$_SERVER['PHP_SELF']."?ticketType=$orderType"?>" method="post">
  <div class="table-responsive">
    <table class='table text-nowrap table-striped'>
      <tr>
        <th class="flag">完了</th>
        <th class="grade">学年</th>
        <th class="part">パート</th>
        <th class="last_name">姓</th>
        <th class="first_name">名</th>
        <th class="amount">枚数</th>
        <th class="limited">配布枚数指定</th>
      </tr>
      <?php
      $stmt = $mysqli->prepare("SELECT personID,grade,last_name,first_name,part,amount,response,orderID FROM tp_Orders INNER JOIN members USING(personID) WHERE finishFlag = 0 AND orderTypeID = ? ORDER BY personID");
      $stmt->bind_param('i',$orderType);
      $result = $stmt->execute();
      $stmt->bind_result($personID,$grade,$last_name,$first_name,$part,$amount,$response,$orderID);
      if($result == NULL){
        echo "<!-- fail select : code : $mysqli->error -->";
        exit();
      }
        while($stmt->fetch()):
      ?>
      <tr>
        <td class="flag">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="orderID[]" value="<?=$orderID?>">
            <?php //メモ：チェックされてたら修正不可にして、取り消し線を表示し、色を灰色にする(javaScript?) ?>
          </div>
        </td>
        <?php //memo:personIDも取得しているので、ここからどこかに送るときは設定できる ?>
        <td class="grade"><?=$grade?></td>
        <td class="part"><?=$part?></td>
        <td class="last_name"><?=$last_name?></td>
        <td class="first_name"><?=$first_name?></td>
        <td class="amount">
          <?php $am = $amount - $response; echo $am; ?>
        </td>   
        <td class="limited">
          <div class="form-group">
            <select name="<?="amount".$orderID?>" class="form-control-sm">
              <option value="<?=$am?>" selected="selected">配布枚数を指定しない</option>
              <?php for($i=$am;$i>0;$i--): ?>
                <option value="<?=$i?>"><?=$i?></option>
              <?php endfor; ?>
            </select>
          </div>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
  <input type="hidden" name="submit" value="submit">
  <input type="hidden" name="orderType" value="<?=$orderType?>">
  <button type="submit" class="btn btn-primary">更新</button>
</form>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>