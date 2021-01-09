<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(MAX_PR_PERM, $USER->id, $mysqli);
  $pageTitle = "チケット預かり利用者リスト";
  $applyStyle = "private";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">チケット預かりを利用するお客様の一覧です</p>
<p class="tx" style="font-color:red;">※個人情報の取り扱いに十分注意してください</p>
<?php 
  //チケット預かりリストを取得する
  $q_reserves = "SELECT lastName, firstName, lastNameKana, firstNameKana, 
  grade, part, last_name, first_name, amount, price FROM tp_Reserves 
  INNER JOIN tp_Orders USING(orderID) INNER JOIN members USING(id) ORDER BY tp_Reserves.lastNameKana";
  $result = $mysqli->query($q_reserves);
  $reserves = [];
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $reserves[] = $row;
  }
  $result->free();
?>
<table class="table-wide">
  <tr class="th">
    <th class="lname-guest">姓</th>
    <th class="fname-guest">名</th>
    <th class="lname-guest">姓(カナ)</th>
    <th class="fname-guest">名(カナ)</th>
    <th class="amount">枚数</th>
    <th class="price">値段</th>
    <th class="member">販売者</th>
  </tr>
  <?php foreach($reserves as $reserve):?>
  <tr class="td">
    <td class="lname-guest"><?=$reserve['lastName']?></td>
    <td class="fname-guest"><?=$reserve['firstName']?></td>
    <td class="lname-guest"><?=$reserve['lastNameKana']?></td>
    <td class="fname-guest"><?=$reserve['firstNameKana']?></td>
    <td class="amount"><?=$reserve['amount']?></td>
    <td class="price"><?=$reserve['price']?></td>
    <td class="member"><?=$reserve['grade'].$reserve['part']." ".$reserve['last_name'].$reserve['first_name']?></td>
  </tr>
  <?php endforeach; ?>
</table>
<button class="btn btn-primary" type="button">印刷する</button>
<?php require_once TP_ROOT.'/include/footer.php'; ?>