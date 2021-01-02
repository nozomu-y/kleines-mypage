<?php 
  /**
   * tp_statusの文字列を表示する文字列に変換する関数
   */
  function printStatusMessage($key){
    if(strcmp($key,"complete_init")==0){
      echo("初期化が完了しました");
    }else if(strcmp($key,"succeed_signin")==0){
      echo("サインインに成功しました");
    }else if(strcmp($key,"wrong-email")==0){
      echo("存在しないメールアドレスです");
    }else if(strcmp($key,"wrong-password")==0){
      echo("パスワードが違います");
    }else{
      echo("statusArea.phpで存在しないkey:\n"+$key);
    }
  }
?>

<div class="status-area">
  <p class="tx">
    <?php
      if(isset($_SESSION['tp_status'])){
        printStatusMessage($_SESSION['tp_status']);
        unset($_SESSION['tp_status']);
      }
      if(isset($_SESSION['tp_error'])){
        printStatusMessage($_SESSION['tp_error']);
        unset($_SESSION['tp_error']);
      }
      if(isset($_POST['tp_status'])){
        printStatusMessage($_POST['tp_status']);
      }
    ?>
  </p>
</div>