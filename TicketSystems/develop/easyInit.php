<?php
  /**
   * 初期設定をワンタッチで行うもの
   */
  define('INIT_PATH', TP_ROOT.'/pages/secret/initialSettings');
  require_once INIT_PATH . '/initTables.php';

  //チケット種別の設定
  $ticketType = [
    "渉外所持",
    "回収済み預かりチケット",
    "団員所持",
    "団員販売済(情宣含む)",
    "オンライン委託",
    "チケット交換済",
    "OVチケット販売済み",
    "OVチケット用にキープ",
    "招待チケット用にキープ",
    "当日券用にキープ"
  ];
  $ticketTypeAmount = [1290, 0, 0, 0, 200, 10, 0, 0, 0, 0];

  require_once INIT_PATH . '/configureTickets.php';
  configureTickets($ticketType, $ticketTypeAmount, $mysqli);

  //権限の設定
  $chief_memberIDs = [1]; //渉外チーフ子
  $pr_memberIDs = [2, 8, 9, 17, 18, 19, 20, 25];  //渉外、フロント

  require_once INIT_PATH . '/configurePermission.php';
  updatePermission($chief_memberIDs, PR_CHIEF_PERM, $mysqli);
  updatePermission($pr_memberIDs, PR_MEMBER_PERM, $mysqli);

  //完了のメッセージを表示
  $_SESSION['tp_status'] = "complete_init";