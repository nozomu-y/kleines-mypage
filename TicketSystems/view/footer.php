<?php
//実験
function getFooter(){
  echo "<!--getFooter-->";
}
require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
?>
<!--end of mainContents-->
  </div>
  <div id="footer">
    <div class="container-fluid">
      <p>このページはやこーによって管理されています。</p>
    </div>
  </div>
  <script src="<?=SERVER?>/view/js/jquery-3.5.1.min.js"></script>
  <script src="<?=SERVER?>/view/js/popper.min.js"></script>
  <script src="<?=SERVER?>/view/js/bootstrap.min.js"></script>
  <!--end of wrapper-->
</div>
</body>
</html>