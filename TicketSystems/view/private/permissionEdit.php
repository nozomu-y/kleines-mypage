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
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  accessFilter();
  $mysqli = dbconnect();

  
  if($_POST['submit']=="permission"){
    require_once(ROOT.'/model/permissionSettingsHandler.php');
    $_SESSION["submit"]="permission";
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }

  //自分の権限を検索
  $stmt = $mysqli->prepare("SELECT permission FROM tp_Permissions WHERE personID = ?");
  $stmt->bind_param('i',$_SESSION['mypage_personID']);
  $stmt->execute();
  $stmt->bind_result($myPermission);
  $result = $stmt->fetch();
  $stmt->close();
  $myPrms = 0;
  if($result==null){
    //アクセスできない
    //渉外以外を弾く処理はprivate全体に置く
  }else{
    $myPrms = $myPermission;
  }

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
  getHeader("権限を編集","private");

  if(isset($_SESSION["submit"])&&$_SESSION["submit"]=="permission"){
    echo "<p>権限を編集しました</p>";
    unset($_SESSION["submit"]);
  }
?>
<h2>権限を編集</h2>
<br>
<p>権限を設定する団員を選択して、「権限を追加」を押してください</p>
<p>渉外チーフは「権限11」、渉外・フロントの団員は「権限12」です</p>
<p>※権限1はWeb管です</p>
<p>権限を削除するときは、「権限を削除」を選択してください</p>
<p>※自分より強い権限を持ってる人に対しての権限編集はできません</p>
<br>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
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
      <?php foreach($rows as $row): ?>
      <tr>
        <td class="flag">
          <?php
            //(permissionが設定されていない団員のデータOR自分の権限より弱いデータ)ANDマスター権限ではないAND自分ではない時のみ、操作できる
            if(($row['permission']==null || $myPrms <= $row['permission'])&&$row['permission']!=1&&$row['personID']!=$_SESSION['mypage_personID']):
          ?>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="personID[]" value="<?php echo $row['personID']; ?>">
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
  <div class="form-row">
    <div class="col-4">
      <select name="permission" class="form-control" id="select-permission">
        <option value="0">権限を削除</option>
        <?php if($myPrms <= 11): ?>
        <option value="11">チーフ権限(11)を付与</option>
        <?php endif; ?>
        <?php if($myPrms <= 12): ?>
        <option value="12">渉外権限(12)を付与</option>
        <?php endif; ?>
      </select>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">権限を更新</button>
</form>
<a class="btn btn-success" href="permissionList.php" role="button">設定完了</a>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>