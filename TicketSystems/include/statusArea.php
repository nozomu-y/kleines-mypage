<?php 
  /**
   * tp_statusの文字列を表示する文字列に変換する関数
   */
  function printStatusMessage($key){
    switch($key){
      case "complete_init":
        echo("初期化が完了しました");
        break;
      case "succeed_signin":
        echo("サインインに成功しました");
        break;
      case "wrong-email":
        echo("存在しないメールアドレスです");
        break;
      case "wrong-password":
        echo("パスワードが違います");
        break;
      case "not-permittied":
        echo("アクセスする権限がありません");
        break;
      case "invalid-page":
        echo("不正な操作です");
        break;
      case "succeed-secret":
        echo("パスワードによる認証に成功しました。<br>続いて初期化を行ってください。");
        break;
      case "edit-perm":
        echo("権限の変更を適用しました");
        break;
      case "complete-perm-edit":
        echo("権限の設定が完了しました");
        break;
      case "succeed-submit":
        echo("送信が完了しました");
        break;
      case "succeed-resolve":
        echo("オーダーへの対応を完了しました");
        break;
      case "succeed-update-promotion":
        echo("情宣の詳細の編集を完了しました");
        break;
      case "succeed-promotion-report":
        echo("情宣の完了報告を完了しました");
        break;
      case "succeed-sold":
        echo("販売報告を完了しました");
        break;
      case "edit-reserve":
        echo("チケット預かりの情報を変更しました");
        break;
      case "delete-reserve":
        echo("チケット預かりの情報を削除しました");
        break;
      case "delete-order":
        echo("オーダーを削除しました");
        break;
      case "delete-promotion":
        echo("情宣を中止しました");
        break;
      case "edit-ticketTotal":
        echo("チケット種別を変更しました");
        break;
      default:
        echo("statusArea.phpで存在しないkey:<br>".$key);
        break;
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