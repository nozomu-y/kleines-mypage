<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  $pageTitle = "チケット所持・配布状況";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">全団員のチケット所持・販売状況の一覧です。</p>
<?php 
  //名簿と販売状況を取得する
  $q_select = "SELECT id, part, grade, last_name, first_name, have, sold FROM members 
  LEFT JOIN tp_MemberTickets USING(id) WHERE members.status = 0 ORDER BY grade ASC";
  $result = $mysqli->query($q_select);
  $members = [];
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $members[] = $row;
  }
  $result->free();
?>
<!-- filters -->
<h2>団員フィルター</h2>
<?php include TP_ROOT."/include/btn-filter/filter.php"; ?>
<h2>集計</h2>
<p class="tx">現在個別リストに表示されている人の合計です。</p>
<p class="tx">フィルターと合わせて使用してください。</p>
<table id="summary">
  <tr class="th">
    <th class="count">人数</th>
    <th class="have">所持</th>
    <th class="sold">販売</th>
  </tr>
  <tr class="td">
    <td class="count">---</td>
    <td class="have">---</td>
    <td class="sold">---</td>
  </tr>
</table>
<h2>個別リスト</h2>
<table class="js-filter-table">
  <tr class="th">
    <th class="grade">学年</th>
    <th class="part">パート</th>
    <th class="last_name">姓</th>
    <th class="first_name">名</th>
    <th class="have">所持</th>
    <th class="sold">販売</th>
  </tr>
  <?php foreach($members as $member):?>
  <tr class="td">
    <td class="grade"><?=$member['grade']?></td>
    <td class="part"><?=$member['part']?></td>
    <td class="last_name"><?=$member['last_name']?></td>
    <td class="first_name"><?=$member['first_name']; ?></td>
    <td class="have"><?=$member['have']?></td>
    <td class="sold"><?=$member['sold']?></td>
  </tr>
  <?php endforeach; ?>
</table>

<!-- import js file -->
<script src="sumAmount.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>