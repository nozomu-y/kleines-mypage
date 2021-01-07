<?php
  require_once __DIR__.'/../../../include/tp_init.php';
  accessFilter(NO_PERM_NUM, $USER->id, $mysqli);

  //一度だけ処理を行う
  if(isset($_POST['process']) && strcmp($_POST['process'], "submit")==0){
    require_once __DIR__.'/report.php';
    $_SESSION['tp_status'] = "succeed-promotion-report";
    header("Location: list.php"); //更新対策
    exit();
  }

  //不正な操作だった時、リストにリダイレクト
  require_once __DIR__ . "/personFilter.php";
  $orderID = htmlspecialchars($_GET['orderID']);
  personFilter($orderID, $USER->id, $mysqli);
  
  //学年を取得
  $q_grade = "SELECT MAX(grade) as max, MIN(grade) as min FROM members";
  $result_grade = $mysqli->query($q_grade);
  $row = $result_grade->fetch_array(MYSQLI_ASSOC);
  $grade_min = $row['min'];
  $grade_max = $row['max'];
  $result_grade->free();
  
  //パートを指定
  $parts = ["S", "A", "T", "B"];
 
  //全団員の名簿を取得する
  $q_member = "SELECT id, part, grade, last_name, first_name FROM members";
  $result_member = $mysqli->query($q_member);
  $members = [];
  while($row = $result_member->fetch_array(MYSQLI_ASSOC)){
    $members[] = $row;
  }
  $result_member->free();

  $pageTitle = "情宣完了報告";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>?orderID=<?=$orderID?>" class="needs-validation" novalidate>
  <?php 
    /**
     * 提出者(id)
     * ・もらったチケットからの売り上げ
     * ・自分のチケットからの売り上げ
     * 同行者(*)
     * ・学年パート名前で選んでもらって、value="id"
     * ・もらったチケットからの売り上げ
     * ・本人のチケットからの売り上げ
     */
  ?>
  <div class="form-block" id="form-block[0]">
    <p class="tx" style="font-weight:bold;">団員1人分の情報を入力してください</p>
    <br>
    <div class="form-group">
      <select class="js-form-item" name="grade[0]" required>
        <option value="">選択する</option>
        <?php for($g = $grade_min; $g <= $grade_max; $g++): ?>
        <option value="<?=$g?>"><?=$g?></option>
        <?php endfor; ?>
      </select>
      <select class="js-form-item" name="part[0]" required>
        <option value="">選択する</option>
        <?php foreach($parts as $part): ?>
        <option value="<?=$part?>"><?=$part?></option>
        <?php endforeach; ?>
      </select>
      <select class="js-form-item" name="id[0]" required>
        <option value="">選択する</option>
        <?php foreach($members as $member): ?>
        <option class="js-grade-<?=$member['grade']?> js-part-<?=$member['part']?>" value="<?=$member['id']?>">
          <?=$member['last_name']." ".$member['first_name']?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <p class="tx">渉外からのチケットからの売り上げ枚数</p>
      <input class="form-text js-form-item js-valid-amount" type="text" name="amount-given[0]" required>
      <div class="required-feedback">枚数を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
      <br>
      <p class="tx">本人のチケットからの売り上げ枚数</p>
      <input class="form-text js-form-item js-valid-amount" type="text" name="amount-self[0]" required>
      <div class="required-feedback">枚数を入力してください</div>
      <div class="format-feedback">半角数字のみ、0以上の整数で入力してください</div>
      <div class="invalid-chars"><,>,&,",'は使用できません。使用したい場合は全角で使用してください。</div>
    </div>
    <button class="btn btn-danger js-fb-remove" style="display:none;">× 同伴者を削除</button>
  </div>
  <input type="hidden" name="process" value="submit">
  <button class="btn btn-success js-fb-add" type="button" formNoValidate>+ 同伴者を追加</button>
  <button class="btn btn-primary js-modal-open js-form-confirm" data-target="confirmModal">入力確認</button>
  <div class="modal js-modal" id="confirmModal">
    <div class="modal-bg js-modal-close"></div>
    <div class="modal-content">
      <div class="modal-header"><div class="modal-title">入力確認</div>
        <span class="modal-cross js-modal-close"><span class="cross1"></span><span class="cross2"></span></span>
      </div>
      <div class="modal-main">
        <p class="tx">この内容で送信してもよろしいですか？</p>
        <div class="js-item-list"></div>
      </div>
      <div class="modal-footer">
        <div class="modal-left">
          <button class="btn btn-secondary js-modal-close">戻る</button>
        </div>
        <div class="modal-right">
          <button class="btn btn-primary" type="submit">送信</button>
        </div>
      </div>
    </div>
  </div>
</form>
<script src="<?=TP_SERVER?>/include/js/form-block-removable.js"></script>
<script src="<?=TP_SERVER?>/include/js/form-modal.js"></script>
<?php require_once TP_ROOT.'/include/footer.php'; ?>