<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	accessFilter();
  $mysqli = dbconnect();

  require_once(ROOT.'/view/header.php');
  getHeader("役職メンバー一覧","private");

	//団員検索
	//$sql = "SELECT personID,part,grade,last_name,first_name,permission FROM members LEFT JOIN tp_Permissions USING(personID) ORDER BY personID";
	$sql = "SELECT part,grade,last_name,first_name,permission FROM tp_Permissions INNER JOIN members USING(personID) ORDER BY personID";
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
	
	if(isset($_SESSION["submit"])&&strcmp($_SESSION["submit"],"reload")==0){
    echo "<p>リストを更新しました</p>";
    unset($_SESSION["submit"]);
  }
?>
<h2>役職メンバー一覧</h2>
<p>渉外関係の権限を持っている人の一覧</p>
<p>変更・削除・メンバーの追加は、ページ下の「権限を編集」から行ってください</p>
<br>
<p>Web管(マスター権限)：1</p>
<p>渉外チーフ権限：11</p>
<p>渉外一般権限：12</p>
<br>
<div class="table-responsive">
    <table class='table text-nowrap table-striped'>
      <tr>
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
<br>
<a class="btn btn-secondary" href="permissionEdit.php" role="button" style="margin-bottom:.5rem">権限を編集</a>
<a class="btn btn-Info" href="<?=SERVER."/model/reloadMaster.php"?>" role="button" style="margin-bottom:.5rem" id="btn-reloadMaster">マスター権限の更新</a>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
	
?>