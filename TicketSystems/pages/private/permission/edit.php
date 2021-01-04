<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  secretFilter(MAX_PR_PERM, $USER->id, $mysqli);
  //一度だけ処理を行う
  if(isset($_POST['process']) && $_POST['process']=='permission'){
    require_once __DIR__.'/../../secret/initialSettings/configurePermission.php';
    $_SESSION['tp_status'] = "edit-perm";
    header("Location:".$_SERVER['PHP_SELF']);
    exit();
  }
  $pageTitle = "権限編集";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<?php //TODO: 説明を、モーダルやアコーディオン等の隠せる形に変更する ?>
<p class="tx">①適用する権限を選んでください。</p>
<p class="tx-sm">・渉外チーフの権限は「権限<?=PR_CHIEF_PERM?>」、渉外・フロントの団員の権限は「権限<?=PR_MEMBER_PERM?>」です</p>
<p class="tx-sm">・権限を削除するときは、「権限を削除」を選択してください</p>
<p class="tx-sm">・自分より強い権限は設定できません</p>
<p class="tx-sm">・注記：権限1はWeb管です</p>
<br>
<p class="tx">②権限を設定する団員を選択して、「権限を適用」を押してください。</p>
<p class="tx-sm">・フィルタリングを使用できます</p>
<p class="tx-sm">・フィルタリングされている団員には、権限は適用されません</p>
<p class="tx-sm">・自分と自分より強い権限の人は選択できません</p>
<br>
<p class="tx">③全ての権限設定が完了したら、「設定を完了」を押してください。</p>
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
  <!-- manipulation selectbox -->
  <h2>操作を選択</h2>
  <div class="selectbox">
    <select name="permission" id="select-permission">
    <?php //TODO: 配列orSQL?とか使って、権限の種類網羅を自動化する ?>
    <?php $permission = getPermission($USER->id, $mysqli);?>
      <option value="<?=NO_PERM_NUM?>">権限を削除</option>
      <?php if(PR_CHIEF_PERM >= $permission): ?>
      <option value="<?=PR_CHIEF_PERM?>">チーフ権限(<?=PR_CHIEF_PERM?>)を付与</option>
      <?php endif; ?>
      <?php if(PR_MEMBER_PERM >= $permission): ?>
      <option value="<?=PR_MEMBER_PERM?>">渉外権限(<?=PR_MEMBER_PERM?>)を付与</option>
      <?php endif; ?>
    </select>
  </div>
  <!-- filters -->
  <h2>団員フィルター</h2>
  <?php include TP_ROOT."/include/btn-filter/filter.php"; ?>
  <!-- form tables -->
  <h2>団員を選択</h2>
  <?php 
    //全団員の名簿を取得する
    $q_select = "SELECT id,part,grade,last_name,first_name,permission FROM members LEFT JOIN tp_Permissions USING(id) ORDER BY grade ASC";
    $result = $mysqli->query($q_select);
    $members = [];
    while($row = $result->fetch_array(MYSQLI_ASSOC)){
      $members[] = $row;
    }
    $result->free();
  ?>
  <table>
    <tr class="th">
      <th class="flag">選択</th>
      <th class="grade">学年</th>
      <th class="part">パート</th>
      <th class="last_name">姓</th>
      <th class="first_name">名</th>
      <th class="permission">権限</th>
    </tr>
    <?php foreach($members as $member):?>
      <tr class="td">
        <td class="flag">
          <?php if($member['permission']!=1 && $member['permission'] >= $permission && $member['id'] != $USER->id): ?>
          <input type="checkbox" class="form-check-input" name="id[]" value="<?=$member['id']?>">
          <?php endif; ?>
        </td>
        <td class="grade"><?=$member['grade']?></td>
        <td class="part"><?=$member['part']?></td>
        <td class="last_name"><?=$member['last_name']?></td>
        <td class="first_name"><?=$member['first_name']; ?></td>
        <td class="permission">
          <?php if($member['permission'] != NO_PERM_NUM ): ?>
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
<button class="btn btn-success js-modal-open" type="button" >設定を完了</button>
<div class="modal js-modal" id="confirmModal">
  <div class="modal-bg js-modal-close"></div>
  <div class="modal-content">
    <div class="modal-header"><div class="modal-title">入力確認</div>
      <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
    </div>
    <div class="modal-main">
      <p class="tx">設定を完了してよろしいですか？</p>
    </div>
    <div class="modal-footer">
      <div class="modal-left">
        <button class="btn btn-secondary js-modal-close">戻る</button>
      </div>
      <div class="modal-right">
        <form method="post" action="list.php">
          <input type="hidden" name="tp_status" value="complete-perm-edit">
          <button class="btn btn-primary" type="submit">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- import js files -->
<script src="<?=TP_SERVER?>/include/js/modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>