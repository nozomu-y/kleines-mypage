<div class="menu-block">
  <input type="checkbox" id="menu-bar01" class="menu-bar">
  <label for="menu-bar01">団員用メニュー</label>
  <div class="menu-item"><p class="nav-link">チケットが欲しいとき</p><a href="<?=TP_SERVER."/pages/everyone/amountForm/index.php?orderTypeID=1"?>"></a></div>
  <div class="menu-item"><p class="nav-link">チケットを売ったとき</p><a href="<?=TP_SERVER."/pages/everyone/salesForm/index.php"?>"></a></div>
  <div class="menu-item"><p class="nav-link">チケットを渉外に返したいとき</p><a href="<?=TP_SERVER."/pages/everyone/amountForm/index.php?orderTypeID=3"?>"></a></div>
  <div class="menu-item"><p class="nav-link">チケットがお客様から返品されたとき</p><a href="<?=TP_SERVER."/pages/everyone/amountForm/index.php?orderTypeID=6"?>"></a></div>
  <div class="menu-item"><p class="nav-link">情宣のアポが取れたとき</p><a href="<?=TP_SERVER."/pages/everyone/promotionRequest/index.php"?>"></a></div>
  <div class="menu-item"><p class="nav-link">情宣一覧確認</p><a href="<?=TP_SERVER."/pages/everyone/promotionList/list.php"?>"></a></div>
  <div class="menu-item"><p class="nav-link">配布済座席確認(指定席限定)</p><a href=""></a></div>
  <div class="menu-item"><p class="nav-link">自分のチケット状況・履歴確認</p><a href="<?=TP_SERVER."/pages/everyone/individualStatus/index.php"?>"></a></div>
</div>
<div class="menu-spacer"></div>
<div class="menu-item"><p class="nav-link">団員用ページTOPへ</p><a href="<?=TP_SERVER."/pages/everyone/index.php"?>"></a></div>
<div class="menu-item"><p class="nav-link">渉外用ページTOPへ</p><a href="<?=TP_SERVER."/pages/private/index.php"?>"></a></div>
<div class="menu-item"><p class="nav-link">渉外チーフ用ページTOPへ</p><a href="<?=TP_SERVER."/pages/secret/index.php"?>"></a></div>
<div class="menu-spacer"></div>
<?php if(strcmp(MODE,"test")==0):?>
<div class="menu-block">
  <input type="checkbox" id="menu-bar-dev" class="menu-bar">
  <label for="menu-bar-dev">開発用メニュー</label>
  <div class="menu-item"><p class="nav-link">サインイン/ユーザー登録</p><a href="<?=TP_SERVER."/develop/signin.php"?>"></a></div>
  <div class="menu-item"><p class="nav-link">サインアウト</p><a href="<?=TP_SERVER."/develop/signout.php"?>"></a></div>
</div>
<?php endif; ?>