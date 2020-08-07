<?php
/**
 * for(各personID){
 * 1,2の場合
 * すでにテーブルにそのpersonIDが存在するかどうかで場合わけ
 *    あったら、update
 *    なかったら、insert
 * 0の場合
 *    select
 *    あったらDELETE
 * post[permission]={0,11,12}で分類
 */
  //ini_set("display_errors",1);
  require_once($_SERVER['DOCUMENT_ROOT'].'/PublicRelationsPage/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  $mysqli = dbconnect();

  $status = ""; //デバッグ用
  //値受け取り
  $IDs = array(); //空の配列を用意
  if(isset($_POST["personID"])&&is_array($_POST["personID"])){
    $IDs = $_POST['personID'];  //権限付与するpersonIDが入った配列
  }
  $permission = $_POST["permission"]; //どの権限を付与するか

  foreach($IDs as $personID){
    //select
    $stmt = $mysqli->prepare('SELECT personID FROM tp_Permissions WHERE personID = ?');
    $stmt->bind_param('i',$personID);
    $stmt->execute();
    $stmt->bind_result($id);
    $result = $stmt->fetch(); //存在していれば1件取得、していなければnull
    $stmt->close();
    //permissionで場合わけ
    if($permission==11 || $permission==12){
      if($result==null){
        //insert
        $stmt2 = $mysqli->prepare('INSERT INTO tp_Permissions (personID,permission) VALUES (?,?)');
        $stmt2->bind_param('ii',$personID,$permission);
        if($stmt2->execute()){
          $status .= "-SucIns$personID";
        }else{
          $status .= "-FailIns$personID";
        }
        $stmt2->close();
      }else{
        //update
        $stmt2 = $mysqli->prepare("UPDATE tp_Permissions SET permission = ? WHERE personID = ?");
        $stmt2->bind_param('ii',$permission,$personID);
        if($stmt2->execute()){
          $status .= "-SucUpd$personID";
        }else{
          $status .= "-FailUpd$personID";
        }
        $stmt2->close();
      }
    }else{
      //delete
      if($result!=null){
        $stmt2 = $mysqli->prepare('DELETE FROM tp_Permissions WHERE personID = ?');
        $stmt2->bind_param('i',$personID);
        if($stmt2->execute()){
          $status .= "-SucDel$personID";
        }else{
          $status .= "-FailDel$personID";
        }
        $stmt2->close();
      }
    }
  }
  echo "<!--$status-->";
  

?>