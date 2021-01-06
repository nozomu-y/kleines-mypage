<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);
  $pageTitle = "情宣一覧";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<?php
	//情宣一覧を検索
  $sql = "SELECT tp_Orders.id, members.grade, members.part, members.last_name, members.first_name,  
  tp_Orders.orderID, groupName, date, tp_Promotions.finishFlag, actualAmount FROM tp_Promotions 
  INNER JOIN tp_Orders USING(orderID) 
  INNER JOIN members USING(id) ORDER BY date";
  $result = $mysqli->query($sql);
  //連想配列で取得
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $promotions[] = $row;
  }
	$result->free();
?>
<p class="tx">情宣一覧です。</p>
<p class="tx">横に長いので、横スクロールしてください。</p>
<p class="tx">内容を変更するとき：「編集」ボタン</p>
<p class="tx">情宣が完了したとき：「完了」ボタン</p>
<p class="tx">※担当者以外は、編集・完了報告はできません。</p>
<table class="table-wide">
  <tr class="th">
    <th class="in-charge">情宣担当者</th>
    <th class="groupName">団体名</th>
    <th class="date">日程</th>
    <th class="finishFlag">状態</th>
    <th class="actualAmount">販売枚数</th>
    <th class="edit">編集</th>
    <th class="done">完了報告</th>
  </tr>
  <?php foreach($promotions as $promotion): ?>
  <tr class="td">
    <td class="in-charge"><?=$promotion['grade'].$promotion['part']." ".$promotion['last_name'].$promotion['first_name']?></td>
    <td class="groupName"><?=$promotion['groupName']?></td>
    <td class="date">
      <?php if($promotion['date'] != NULL){
          echo $promotion['date'];
        }else{
          echo "未定";
        }?>
    </td>
    <?php /*
      TODO: 
        ・中止ボタンを作る or 編集→中止ができるようにする を決める
        ・編集、完了報告に、可能ならformを使わずに遷移したい(getにする？)
          getにした場合、遷移した先で妥当性の判断を行う(finishFlag、deleteFlagとかで)
          ↑ユーザー妥当性判断と一緒に行う
    */ ?>
    <td class="finishFlag">
      <?php if($promotion['finishFlag']==0){
        echo "未完了";
      }else{
        echo "完了";
      }?>
    </td>
    <td class="actualAmount">
      <?php if($promotion['actualAmount'] != null){
        echo $promotion['actualAmount'];
      }else{
        echo "---";
      }
      ?>
    </td>
    <td class="edit">
      <?php if($promotion['finishFlag']==0): ?>
      <form action="#" method="post">
        <input type="hidden" name="orderID" value="<?=$promotion['orderID']?>">
        <input type="hidden" name="personID" value="<?=$promotion['id']?>">
        <input type="submit" class="btn btn-secondary" value="編集">
      </form>
      <?php endif; ?>
    </td>
    <td class="done">
    <?php if($promotion['finishFlag']==0): ?>
      <form action="#" method="post">
        <input type="hidden" name="orderID" value="<?=$promotion['orderID']?>">
        <input type="hidden" name="personID" value="<?=$promotion['id']?>">
        <input type="submit" class="btn btn-primary" value="完了">
      </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php require_once TP_ROOT.'/include/footer.php'; ?>