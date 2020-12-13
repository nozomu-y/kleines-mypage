<div id="nav-drawer">
  <input id="nav-input" type="checkbox" class="nav-unshown">
  <label id="nav-open" for="nav-input"><span></span></label>
  <label id="nav-close" class="nav-unshown" for="nav-input"></label>
  <!--読み込み先に合わせて中身を変える-->
  <div id="nav-content">
    <div id="nav-header">
      <label id="nav-cross" for="nav-input">
        <span id="cross1"></span><span id="cross2"></span>
      </label>
    </div>
    <div id="nav-main">
      <?php $return=include(ROOT."/include/nav-block.php");?>
    </div>
  </div>
</div>