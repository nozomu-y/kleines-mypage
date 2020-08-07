<?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
  function assignSettingProcess($process){
    //post['process']に応じて振り分ける、process=initならinitialSettingshandler, =reloadならmemberTicket
    switch($process){
      case 'init':
        require_once(ROOT.'/model/initialSettingsHandler.php');
        break;
      default:
        break;
    }
  }
?>