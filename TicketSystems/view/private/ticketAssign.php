<?php
  //ini_set("display_errors",1);
  //error_reporting(E_ALL);
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  accessFilter();
  $mysqli = dbconnect();

  //更新処理
  if(strcmp($_POST['submit'],"update")==0){
    //model呼び出し
    require_once(ROOT.'/model/changeAssignHandler.php');
    //SESSION更新
    $_SESSION['tp_status'] = "updateAssign";
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
  }
  //チケット区分追加処理
  if(strcmp($_POST['submit'],"add")==0){
    //model呼び出し

    //SESSION更新
    $_SESSION['tp_status'] = "addAssign";
    header("Location:{$_SERVER['PHP_SELF']}");
    exit();
  }

  //tp_TicketTotalの内容の取得
  $sql = "SELECT ticketTypeCode,ticketTypeValue,amount FROM tp_TicketTotal";
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

  require_once(ROOT.'/view/header.php');
  getHeader("チケット割り当て確認・変更","private");

  //処理完了時の表示内容
  if(isset($_SESSION["tp_status"])&&strcmp($_SESSION['tp_status'],"updateAssign")==0){
    echo "<p>割り当て変更が完了しました</p>";
    unset($_SESSION["tp_status"]);
  }
  //チケット区分追加時の表示内容
  if(isset($_SESSION["tp_status"])&&strcmp($_SESSION['tp_status'],"addAssign")==0){
    echo "<p>チケット区分の追加が完了しました</p>";
    unset($_SESSION["tp_status"]);
  }
?>
<h2>チケット割り当て確認・変更</h2>
<p>contents</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <div class="table-responsive">
    <table class='table table-striped'>
      <tr>
        <th>チケットの種類</th>
        <th>合計枚数</th>
        <th>変更枚数</th>
        <th>増減</th>
        <th>削除</th>
      </tr>
      <?php foreach($rows as $row): ?>
      <tr>
        <td><?=$row['ticketTypeValue']?></td>
        <td><?=$row['amount']?></td>
        <?php if($row['ticketTypeCode']>4): ?>
        <!--変更枚数-->
        <td>
          <div class="form-group">
            <input class="form-control-sm" type="text" name="amount[<?=$row['ticketTypeCode']?>]">
          </div>
        </td>
        <!--増減-->
        <td>
          <div class="form-group">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-outline-secondary active">
                <input type="radio" name="AddRem[<?=$row['ticketTypeCode']?>]" id="add<?=$row['ticketTypeCode']?>" value="add" autocomplete="off" checked>&plus;
              </label>
              <label class="btn btn-outline-secondary">
                <input type="radio" name="AddRem[<?=$row['ticketTypeCode']?>]" id="rem<?=$row['ticketTypeCode']?>" value="rem" autocomplete="off">&minus;
              </label>
            </div>
          </div>
        </td>
        <!--削除-->
        <td>
          <?php //memo 枚数が残っていたら削除できないようにする ?>
          <button type="button" id="btn-remove-<?=$row['ticketTypeCode']?>" class="btn btn-danger" data-toggle="modal" data-target="#confirmRemove">削除</button>
          <!--ConfirmRemove-->
          <div class="modal fade" id="confirmRemove" tabindex="-1" role="dialog" aria-labelledby="label1" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="label1">削除確認</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>このチケット区分を削除しますか？</p><br>
                  <p><?=$row['ticketTypeValue']?></p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">戻る</button>
                  <button type="button" class="btn btn-danger">削除する</button>
                  <?php //memo 削除ボタンを、フォームとは独立で、例えばURL渡しでmodelに渡す等して削除用のモデルに投げる ?>
                </div>
              </div>
            </div>
          </div>
        </td>
        <?php else: ?>
          <td></td><td></td><td></td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <input type="hidden" name="submit" value="update">
  <button type="submit" class="btn btn-primary">割り当て変更</button>
</form>
<button type="button" id="btn-add-assign" class="btn btn-success" data-toggle="modal" data-target="#addAssign">チケット区分追加</button>
<!--addAssign-->
<div class="modal fade" id="addAssign" tabindex="-1" role="dialog" aria-labelledby="label-add" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="label-add">チケット区分追加</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>追加するチケット区分の情報を入力してください</p>
          <div class="form-group">
            <p>区分名</p>
            <input type="text" class="form-control" name="ticketTypeName">
          </div>
          <div class="form-group">
            <p>初期枚数(渉外所持から移動)</p>
            <input tyoe="text" class="form-control" name="amount">
          </div>
          <input type="hidden" name="submit" value="add">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">戻る</button>
          <button type="submit" class="btn btn-primary">追加する</button>
        </div>
      </form>
    </div>
  </div>
</div>
<br>
<p><a href="index.php">渉外用チケット管理ページトップに戻る</a></p>
<br>
<?php
  require_once(ROOT.'/view/footer.php');
?>