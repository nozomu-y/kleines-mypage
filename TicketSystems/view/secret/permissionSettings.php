<?php
  /***
   * 権限設定を行う
   * この画面で、人を追加・権限を設定を行う(イメージはLINEのチャット開始時)
   *    団員一覧を表示する(フィルタリング機能もあとでつける)
   *    チェックを入れていく
   *    「権限を追加」->{1,2}をクリックするとその属性が追加される(フォームでセルフ送信->permissionSettingsHandler)
   *    「設定完了」を押すと次の画面(finishSettings)へ移動
   * 
   * 先頭でticketSettingsHandler.phpを呼び出す
   * 
   */
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSecretSession();
  $mysqli = dbconnect();

  
  if($_POST['submit']=="permission"){
    require_once(ROOT.'/model/permissionSettingsHandler.php');
    $_SESSION["submit"]="permission";
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }
  
  if($_POST['submit']=="ticket"){
    require_once(ROOT."/model/ticketSettingsHandler.php");
    $_SESSION["submit"]="ticket";
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }
  
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

  //団員検索
  $sql = "SELECT personID,part,grade,last_name,first_name,permission FROM members LEFT JOIN tp_Permissions USING(personID) ORDER BY personID";
  $result = $mysqli->query($sql);
  if($result == NULL){
    echo "<!-- fail select : code : $mysqli->error -->";
    exit();
  }
  //連想配列で取得
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $rows[] = $row;
  }
  //結果セットを解放
  $result->free();
  
  require_once(ROOT.'/view/header.php');
  getHeader("権限設定","secret");

  if(isset($_SESSION["submit"])&&$_SESSION["submit"]=="ticket"){
    echo "<p>チケット設定を完了しました</p>";
    unset($_SESSION["submit"]);
  }
  if(isset($_SESSION["submit"])&&$_SESSION["submit"]=="permission"){
    echo "<p>権限を設定しました</p>";
    unset($_SESSION["submit"]);
  }
?>
<h2>権限設定</h2>
<br>
<p>権限を設定する団員を選択して、「権限を追加」を押してください</p>
<p>渉外チーフは「権限11」、渉外・フロントの団員は「権限12」です</p>
<p>※権限1はWeb管です</p>
<p>権限を削除するときは、「権限を削除」を選択してください</p>
<p>※この設定は後から変更できます</p>
<br>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
  <h3>操作を選択</h3>
  <div class="form-row">
    <div class="col-4">
      <select name="permission" class="form-control" id="select-permission">
        <option value="0">権限を削除</option>
        <option value="11">チーフ権限(11)を付与</option>
        <option value="12">渉外権限(12)を付与</option>
      </select>
    </div>
  </div>
  <h3>フィルタリング</h3>
  <p>パート</p>
  <div class="row">
    <button class="btn btn-secondary" name="filter-part-clear" id="filter-part-clear">Clear</button>
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
      <label class="btn btn-outline-secondary">
        <input type="checkbox" name="filter-part[]" value="S" autocomplete="off">Sop
      </label>
      <label class="btn btn-outline-secondary">
        <input type="checkbox" name="filter-part[]" value="A" autocomplete="off">Alt
      </label>
      <label class="btn btn-outline-secondary">
        <input type="checkbox" name="filter-part[]" value="T" autocomplete="off">Ten
      </label>
      <label class="btn btn-outline-secondary">
        <input type="checkbox" name="filter-part[]" value="B" autocomplete="off">Bas
      </label>
    </div>
  </div>
  <p>学年</p>
  <div class="row">
    <button class="btn btn-secondary" name="filter-grade-clear" id="filter-grade-clear">Clear</button>
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
      <?php for($grd=$newestGrade;$grd>=$oldestGrade;$grd--): ?>
      <label class="btn btn-outline-secondary">
        <input type="checkbox" name="filter-grade[]" value="<?=$grd?>" autocomplete="off"><?=$grd?>
      </label>
      <?php endfor; ?>
    </div>
  </div>
  <h3>人を選択</h3>
  <div class="table-responsive">
    <table class='table text-nowrap table-striped'>
      <tr>
        <th class="flag">選択</th>
        <th class="grade">学年</th>
        <th class="part">パート</th>
        <th class="last_name">姓</th>
        <th class="first_name">名</th>
        <th class="permission">権限</th>
      </tr>
      <?php
        foreach($rows as $row):
      ?>
      <tr>
        <td class="flag">
          <?php if($row['permission']==null || $row['permission']!=1): ?>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="personID[]" value="<?php echo $row['personID']; ?>">
            <?php //メモ：チェックされてたら修正不可にして、取り消し線を表示し、色を灰色にする(javaScript?) ?>
          </div>
            <?php endif; ?>
        </td>
        <td class="grade"><?php echo $row['grade']; ?></td>
        <td class="part"><?php echo $row['part']; ?></td>
        <td class="last_name"><?php echo $row['last_name']; ?></td>
        <td class="first_name"><?php echo $row['first_name']; ?></td>
        <td class="permission">
          <?php
            if($row['permission']!=null) echo $row['permission'];
          ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </table>
  </div>
  <input type="hidden" name="submit" value="permission">

  <button type="submit" class="btn btn-primary">権限を更新</button>
</form>
<a class="btn btn-success " href="finishSettings.php" role="button">設定完了</a>
<br>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>