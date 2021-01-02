<?php
  //操作対象のpersonのid、付与する権限を取得
  $personIDs = array();
  if(isset($_POST["personID"]) && is_array($_POST["personID"])){
    $personIDs = $_POST['personID'];
  }
  $permission = $_POST["permission"]; //付与するpermission

  //更新操作
  foreach($personIDs as $personID){
    updatePermission($personID, $permission, $mysqli);
  }

  /**
   * あるメンバーの権限を更新する関数
   * @param personID 
   * @param permission
   * @param mysqli mysqliオブジェクト
   */
  function updatePermission($personID, $permission, $mysqli){
    //更新
    $stmt_update = $mysqli->prepare("UPDATE tp_Permissions SET permission = ? WHERE personID = ?");
    $stmt_update->bind_param('ii', $permission, $personID);
    $res_update = $stmt_update->execute();
    $stmt_update->close();
    if(!$res_update){
      //error
    }
  }



