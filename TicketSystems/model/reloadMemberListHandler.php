<?php
  ini_set('display_errors',1);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  $mysqli = dbconnect();

  //団員を追加する
  /***
   * members - tp_MemberTickets = 未挿入の団員
   * →その団員のpersonIDをinsert
   * SELECT personID,have FROM members LEFT JOIN tp_MemberTickets USING(personID) WHERE have IS NULL
   */

   $q1 = "INSERT INTO tp_MemberTickets (personID) SELECT personID FROM members LEFT JOIN tp_MemberTickets USING(personID) WHERE have IS NULL";
   if($mysqli->query($q1)){
    $status = "SucInsMemTic";
   }else{
    $status = "FailInsMemTic:$mysqli->error";
   }
   dbclose($mysqli);
?>