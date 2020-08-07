<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  accessFilter();
	$mysqli = dbconnect();
?>
<?php
  require_once(ROOT.'/view/header.php');
  getHeader("預かり一覧","private");

  //預かり一覧情報の読み込み
  //団員の学年パート名前・利用者の名前・price,amount
  $sql = "SELECT tp_Reserves.lastName,tp_Reserves.firstName,tp_Reserves.lastNameKana,tp_Reserves.firstNameKana,grade,part,members.last_name,members.first_name,members.kana,amount,price FROM tp_Reserves INNER JOIN tp_Orders ON tp_Reserves.orderID = tp_Orders.orderID INNER JOIN members ON members.personID = tp_Orders.personID ORDER BY tp_Reserves.lastNameKana";
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
<h2>預かり一覧</h2>
<p>※注意　個人情報をたくさん含んでいます。情報の取り扱いには気をつけてください。</p>
<br>
<div class="table-responsive-sm">
	<table class='table text-nowrap table-striped' id="reserveTable">
    <tr class="titleRow" style="background-color:#bbbbbb;">
      <th colspan="4" style="border-right:1px solid rgba(0,0,0,.35)">お客様</th>
			<th colspan="5" style="border-right:1px solid rgba(0,0,0,.35)">団員</th>
			<th colspan="2">チケット情報</th>
    </tr>
    <tr class="titleRow" style="background-color:#bbbbbb;">
      <th class="guest_lastName">姓</th>
      <th class="guest_firstName">名</th>
      <th class="guest_lastNameKana">姓(カナ)</th>
      <th class="guest_firstNameKana">名(カナ)</th>
			<th class="member_grade">学年</th>
      <th class="member_part">パート</th>
			<th class="member_lastName">姓</th>
			<th class="member_firstName">名</th>
			<th class="member_kana">カナ</th>
			<th class="amount">枚数</th>
			<th class="price">値段</th>
    </tr>
    <?php foreach($rows as $row): ?>
      <tr>
        <td class="guest_lastName"><?=$row["lastName"]?></td>
        <td class="guest_firstName"><?=$row["firstName"]?></td>
        <td class="guest_lastNameKana"><?=$row["lastNameKana"]?></td>
        <td class="guest_firstNameKana"><?=$row["firstNameKana"]?></td>
        <td class="member_grade"><?=$row["grade"]?></td>
        <td class="member_part"><?=$row["part"]?></td>
        <td class="member_lastName"><?=$row["last_name"]?></td>
        <td class="member_firstName"><?=$row["first_name"]?></td>
        <td class="member_kana"><?=$row["kana"]?></td>
        <td class="amount"><?=$row["amount"]?></td>
        <td class="price"><?=$row["price"]?></td>
      </tr>
    <?php endforeach;?>
  </table>
</div>
<button class="btn btn-primary">印刷</button>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>