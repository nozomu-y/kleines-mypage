<?php
  //操作対象のpersonのid、付与する権限を取得
  $ids = array();
  if(isset($_POST["id"]) && is_array($_POST["id"])){
    $ids = $_POST['id'];
  }
  $permission = $_POST["permission"]; //付与するpermission

  //更新操作
  foreach($ids as $id){
    updatePermission($id, $permission, $mysqli);
  }

  /**
   * あるメンバーの権限を更新する関数
   * @param id 
   * @param permission
   * @param mysqli mysqliオブジェクト
   */
  function updatePermission($id, $permission, $mysqli){
    //更新
    $stmt_update = $mysqli->prepare("UPDATE tp_Permissions SET permission = ? WHERE id = ?");
    $stmt_update->bind_param('ii', $permission, $id);
    $res_update = $stmt_update->execute();
    $stmt_update->close();
    if(!$res_update){
      //error
    }
  }



