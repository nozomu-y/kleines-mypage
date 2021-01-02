<?php 
  /**
   * tp_statusの文字列を表示する文字列に変換する関数
   */
  function printStatusMessage($key){
    if(strcmp($key,"complete_init")==0){
      echo("初期化が完了しました\n");
    }else{
      echo($key);
    }
  }
?>

<div class="status-area">
  <?php
    if(isset($_SESSION['tp_status'])){
      printStatusMessage($_SESSION['tp_status']);
      unset($_SESSION['tp_status']);
    }
    if(isset($_POST['tp_status'])){
      printStatusMessage($_POST['tp_status']);
    }
  ?>
</div>