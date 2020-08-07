<?php
  //マスター権限を持つ人をmembersから検索し、tp_Permissionsを更新
  ini_set('display_errors',1);
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
	startSession();
	accessFilter();
  $mysqli = dbconnect();

  //permission=1の人を削除する
  $status = "";
  $q1 = "DELETE FROM tp_Permissions WHERE permission = 1";
  if($mysqli->query($q1)){
    $status .= "SucDel";
  }else{
    $status .= "FailDel";
  }

  //マスター権限を持つ人をmembersから検索
  $q2 = "SELECT personID FROM members WHERE admin = 1";
  $result = $mysqli->query($q2);
  if($result==NULL){
    $status.= "-FailSel:$mysqli->error";
  }else{
    $status .="-SucSel";
  }

  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $rows[] = $row;
  }
  //結果セットを解放
  $result->free();
  
  //admin==1である全てのデータに対して、更新or挿入
  foreach($rows as $row){
    $stmt = $mysqli->prepare("INSERT INTO tp_Permissions (personID, permission) VALUES (?, 1) ON DUPLICATE KEY UPDATE permission=1");
    $stmt->bind_param('i',$row['personID']);
    if($stmt->execute()){
      $status .= "-SucInsORUpd".$row['personID'];
    }else{
      $status .= "-FailInsORUpd".$row['personID'];
    }
  }
  dbclose($mysqli);
  $_SESSION["submit"] = "reload";
  header("Location: ".SERVER."/view/private/permissionList.php?status=$status");
  exit();
?>