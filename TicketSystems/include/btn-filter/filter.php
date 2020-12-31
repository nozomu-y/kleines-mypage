<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/Common/dbconnect.php');
  $parts = ["S", "A", "T", "B"];
  $GRADE_NUM = 5; //4学年+上級生として5つにまとめる
  $q_select = "SELECT DISTINCT(grade) FROM members ORDER BY grade DESC LIMIT $GRADE_NUM";  //上級生をまとめるために逆順で5つ取得
  $result = $mysqli->query($q_select);
  if($result==NULL){
    //error
  }
  $grades_rev = [];
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $grades_rev[] = $row['grade'];
  }
  $result->free();
  $grades_rev[$GRADE_NUM-1] .= "以上";  //"(5年生の入学年度)以上"とし、上級生をまとめる
  $grades = array_reverse($grades_rev);
?>
<script src="<?=SERVER?>/include/btn-filter/filter.js"></script>
<div class="js-filter-btn js-filter-part">
  <p class="tx" style="font-size:18px;">学年</p>
  <button class="btn btn-secondary js-filter-clear" name="filter-part-clear" style="display:inline;">Clear</button>
  <button class="btn btn-secondary js-filter-all" name="filter-part-all" style="display:inline;">All</button><br>
  <?php foreach($parts as $part): ?>
  <input class="filter-checkbox" type="checkbox" name="filter-part[]" id="filter-<?=$part?>" value="<?=$part?>" autocomplete="off" style="display:none;" checked>
  <label for="filter-<?=$part?>" class="btn btn-filter"><?=$part?></label>
  <?php endforeach; ?>
</div>

<div class="js-filter-btn js-filter-grade">
<p class="tx" style="font-size:18px;">パート</p>
  <button class="btn btn-secondary js-filter-clear" name="filter-grade-clear" style="display:inline;">Clear</button>
  <button class="btn btn-secondary js-filter-all" name="filter-grade-all" style="display:inline;">All</button><br>
  <?php foreach($grades as $grade): ?>
  <input class="filter-checkbox" type="checkbox" name="filter-part[]" id="filter-<?=$grade?>" value="<?=$grade?>" autocomplete="off" style="display:none;" checked>
  <label for="filter-<?=$grade?>" class="btn btn-filter"><?=$grade?></label>
  <?php endforeach; ?>
</div>