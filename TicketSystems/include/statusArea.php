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
    }else if(strcmp($key,"not-permittied")==0){
      echo("アクセスする権限がありません");
    }else if(strcmp($key,"invalid-page")==0){
      echo("不正な操作が行われました");
    }else if(strcmp($key,"succeed-secret")==0){
      echo("パスワードによる認証に成功しました。<br>続いて初期化を行ってください。");
    }else if(strcmp($key,"edit-perm")==0){
      echo("権限の変更を適用しました");
    }else if(strcmp($key,"complete-perm-edit")==0){
      echo("権限の設定が完了しました");
    }else if(strcmp($key,"succeed-submit")==0){
      echo("送信が完了しました");
    }else{
      echo("statusArea.phpで存在しないkey:<br>"+$key);
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
      if(isset($_POST['tp_status'])){
        printStatusMessage($_POST['tp_status']);
      }
    ?>
  </p>
</div>