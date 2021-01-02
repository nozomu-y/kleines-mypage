<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  //どのページから送られてきたかによって、一度だけ処理を行う
  //TODO: ブラウザバックの対応
  if(isset($_POST['process']) && $_POST['process']=="ticket"){
    require_once __DIR__.'/configureTickets.php';
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }

  if(isset($_POST['process']) && $_POST['process']=='permission'){
    require_once __DIR__.'/configurePermission.php';
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }

  $pageTitle = "権限設定";
  $applyStyle = "secret";
  require_once TP_ROOT.'/include/header.php';
?>
<?php //TODO: 説明を、モーダルやアコーディオン等の、表示・非表示を変更できる形にすると、スクロールが減っていいのではないか？ ?>
<p class="tx">権限を設定する団員を選択して、「権限を追加」を押してください。</p>
<p class="tx">渉外チーフの権限は「権限11」、渉外・フロントの団員の権限は「権限12」です。</p>
<p class="tx">※注記：権限1はWeb管です</p>
<p class="tx">権限を削除するときは、「権限を削除」を選択してください。</p>
<p class="tx">※注記：この設定は後から変更できます</p><br>
<!-- filters -->
<h2>団員フィルター</h2>
<?php include TP_ROOT."/include/btn-filter/filter.php"; ?>
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
  <!-- manipulate select -->
  <h2>操作を選択</h2>
  <div class="selectbox">
    <select name="permission" id="select-permission">
      <option value="999">権限を削除</option>
      <option value="11">チーフ権限(11)を付与</option>
      <option value="12">渉外権限(12)を付与</option>
    </select>
  </div>
  <!-- form tables -->
  <h2>団員を選択</h2>
  <?php 
    //全団員の名簿を取得する
    $q_select = "SELECT personID,part,grade,last_name,first_name,permission FROM members LEFT JOIN tp_Permissions USING(personID) ORDER BY grade ASC";
    $result = $mysqli->query($q_select);
    $members = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)){
      $members[] = $row;
    }
    //結果セットを解放
    $result->free();
  ?>
  <table>
    <tr>
      <th class="flag">選択</th>
      <th class="grade">学年</th>
      <th class="part">パート</th>
      <th class="last_name">姓</th>
      <th class="first_name">名</th>
      <th class="permission">権限</th>
    </tr>
    <?php foreach($members as $member):?>
      <tr>
        <td class="flag">
          <?php if($member['permission']!=1): ?>
          <input type="checkbox" class="form-check-input" name="personID[]" value="<?=$member['personID']?>">
          <?php endif; ?>
        </td>
        <td class="grade"><?=$member['grade']?></td>
        <td class="part"><?=$member['part']?></td>
        <td class="last_name"><?=$member['last_name']?></td>
        <td class="first_name"><?=$member['first_name']; ?></td>
        <td class="permission">
          <?php if($member['permission']!=999 ): ?>
          <?=$member['permission']?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <!-- accept button -->
  <input type="hidden" name="process" value="permission">
  <button class="btn btn-primary" type="submit">権限を適用</button>
</form>
<!-- finish button -->
<form method="post" action="../index.php">
  <input type="hidden" name="tp_status" value="complete_init">
  <button class="btn btn-success" type="submit">設定を完了</button>
</form>
<?php require_once TP_ROOT.'/include/footer.php'; ?>