<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  $pageTitle = "役職メンバーリスト";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">権限がある団員の一覧です。</p>
<?php //TODO: 配列(連想配列？)とか使って、権限の種類網羅を自動化する ?>
<p class="tx">権限1：web管権限</p>
<p class="tx">権限<?=PR_CHIEF_PERM?>：渉外チーフ権限</p>
<p class="tx">権限<?=PR_MEMBER_PERM?>：渉外権限</p>
<?php 
  //権限保持者の名簿を取得する
  $q_select = "SELECT id,part,grade,last_name,first_name,permission FROM members 
  LEFT JOIN tp_Permissions USING(id) WHERE permission < ".NO_PERM_NUM." ORDER BY grade ASC";
  $result = $mysqli->query($q_select);
  $members = [];
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $members[] = $row;
  }
  $result->free();
?>
<table>
  <tr class="th">
    <th class="grade">学年</th>
    <th class="part">パート</th>
    <th class="last_name">姓</th>
    <th class="first_name">名</th>
    <th class="permission">権限</th>
  </tr>
  <?php foreach($members as $member):?>
  <tr class="td">
    <td class="grade"><?=$member['grade']?></td>
    <td class="part"><?=$member['part']?></td>
    <td class="last_name"><?=$member['last_name']?></td>
    <td class="first_name"><?=$member['first_name']; ?></td>
    <td class="permission"><?=$member['permission']?></td>
  </tr>
  <?php endforeach; ?>
</table>
<a class="btn btn-primary" href="<?=TP_SERVER?>/pages/private/permissionEdit/index.php">権限を編集</a>

<?php require_once TP_ROOT.'/include/footer.php'; ?>