<?php
  require_once __DIR__.'/../../include/tp_init.php';
  //ここにはaccessFilterをつけない(ここだけは表示できるように)
  $pageTitle = "団員用TOP";
  $applyStyle = "everyone";
  require_once TP_ROOT.'/include/header.php';
?>
<p class="tx">チケットの申し込み・各種手続きを行うサイトです</p>
<h2>使用時の注意</h2>
<p class="tx">
  JavaScriptを有効にしてください！<br>
  さもないと正常に動作しません。<br>
  有効にする方法を載せたい<br>
  JavaScriptが有効になっているか確認↓<br>
  <a class="btn btn-primary" href="#">確認</a>
  <br>↑クリックしてリンク先で遊べた人は有効！みたいな
</p>
<h2>渉外からのお知らせ</h2>
<p class="tx">ニュース形式で表示できたりしたらいいよね</p>
<h2>CK_Ticketの使い方</h2>
<p class="tx">ケース別で紹介するとか動画・マニュアル作るとか</p>
<?php require_once TP_ROOT.'/include/footer.php'; ?>