<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  $pageTitle = "チケット種別の確認・変更";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">チケット種別の一覧です</p>
<?php 
  //チケット種別を取得
  $q_select = "SELECT ticketTypeCode, ticketTypeValue, amount FROM tp_TicketTotal";
  $result = $mysqli->query($q_select);
  $ticketTypes = [];
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $ticketTypes[] = $row;
  }
  $result->free();
?>
<table>
  <tr class="th">
    <th class="ticketTypeValue">チケット種別</th>
    <th class="amount">枚数</th>
  </tr>
  <?php foreach($ticketTypes as $type):?>
  <tr class="td">
    <td class="ticketTypeValue"><?=$type['ticketTypeValue']?></td>
    <td class="amount"><?=$type['amount']?></td>
  </tr>
  <?php endforeach; ?>
</table>
<a class="btn btn-primary" href="#">チケット種別を編集</a>

<?php require_once TP_ROOT.'/include/footer.php'; ?>