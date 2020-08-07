<?php
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

  require_once(ROOT.'/view/header.php');
  getHeader("チケット所持・配布一覧","private");
?>
<h2>チケット所持・配布一覧</h2>
<p>開発用メモ：ここでパートごと・学年ごとのフィルタリングとかできるといいね</p>
<p>フィルターごとに平均販売枚数とか合計とかノルマ達成率とか出せるといいね</p>
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
<?php
  $sql = "SELECT personID,part,grade,last_name,first_name,have,sold FROM tp_MemberTickets INNER JOIN members USING(personID) ORDER BY personID";
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
?>
<div class="table-responsive">
  <table class='table text-nowrap table-striped'>
    <tr>
      <th class="personID">ID</th>
      <th class="grade">学年</th>
      <th class="part">パート</th>
      <th class="last_name">姓</th>
      <th class="first_name">名</th>
      <th class="have">所持枚数</th>
      <th class="sold">売上枚数</th>
    </tr>
    <?php foreach($rows as $row): ?>
    <tr>
      <td class="personID"><?php echo $row['personID']; ?></td>
      <td class="grade"><?php echo $row['grade']; ?></td>
      <td class="part"><?php echo $row['part']; ?></td>
      <td class="last_name"><?php echo $row['last_name']; ?></td>
      <td class="first_name"><?php echo $row['first_name']; ?></td>
      <td class="have"><?php echo $row['have']; ?></td>
      <td class="sold"><?php echo $row['sold']; ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>