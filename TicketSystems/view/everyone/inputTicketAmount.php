<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  startSession();
  
  //orderTypeによって表示内容を変える
  if($_GET['orderType']==1){  //request
    $title = "チケット希望フォーム";
  }else if($_GET['orderType']==3){  //want_return
    $title = "チケット返却希望フォーム";
  }else if($_GET['orderType']==6){  //cancel
    $title = "チケットキャンセル報告";
  }else{
    $_SESSION['tp_status'] = "invalidPage";
    header("Location: ".SERVER."/view/everyone/index.php");
    exit();
  }

	require_once(ROOT.'/view/header.php');
	getHeader($title,"everyone");
?>
<h2><?=$title?></h2>
<br>
<form action="finishOrder.php" method="post" class="needs-validation" novalidate>
	<div class="form-group">
		<p class="componentsTitle">
    <?php if($_GET['orderType']==1): ?>
    希望枚数
    <?php elseif($_GET['orderType']==3): ?>
    返却枚数
    <?php elseif($_GET['orderType']==6): ?>
    キャンセル枚数
    <?php endif; ?>
    </p>
    <input type="text" class="form-control 6 md-6" name="amount" id="amount" placeholder="枚数を入力してください" required>
		<div class="invalid-feedback">
			枚数を入力してください
		</div>
	</div>
	<div class="form-group">
    <input type="hidden" name="personID" value="<?=h($_SESSION['mypage_personID'])?>">
		<input type="hidden" name="orderType" value="<?=h($_GET['orderType'])?>">
	</div>
  <button type="button" id="btn-confirm" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
    入力確認
    <!--ここでformValidationが発動するようにする-->
  </button>
  <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="label1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="label1">入力確認</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>この内容で送信しますか？</p><br>
          <div class="row">
            <div class="col-4"><p>送信者</p></div>
            <div class="col-8"><p>
              <?=$_SESSION['grade'].$_SESSION['part']." ".$_SESSION['lname'].$_SESSION['fname']?>
            </p></div>
          </div>
          <div class="row">
            <div class="col-4"><p>オーダー種別</p></div>
            <div class="col-8"><p>
              <?php
                if($_GET['orderType']==1){  //request
                  echo "チケット希望";
                }else if($_GET['orderType']==3){  //want_return
                  echo "チケット返却希望";
                }else if($_GET['orderType']==6){  //cancel
                  echo "チケットキャンセル報告";
                }
              ?>
            </p></div>
          </div>
          <div class="row">
            <div class="col-4"><p>枚数</p></div>
            <div class="col-8"><p>
              ??
              <!--jsで内容を取得してここに表示する-->
            </p></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">修正する</button>
          <button type="submit" class="btn btn-primary">送信する</button>
        </div>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript" src="<?=SERVER?>/view/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?=SERVER?>/view/js/validate-confirm.js"></script>
<p><a href="index.php">トップ画面に戻る</a></p>
<?php
	require_once(ROOT.'/view/footer.php');
?>
	