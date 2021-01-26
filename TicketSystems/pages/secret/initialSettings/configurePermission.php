<?php
  /**
   * メンバーの権限を更新する関数
   * @param ids 団員のidが入った配列
   * @param permission idsの団員に大して設定する権限の数値
   * @param mysqli
   */
  function updatePermission($ids, $permission, $mysqli){
    //null chack
    if(!isset($ids) || !is_array($ids)){
      $ids = array();
    }
    //update each member
    foreach($ids as $id){
      $stmt_update = $mysqli->prepare("UPDATE tp_Permissions SET permission = ? WHERE id = ?");
      $stmt_update->bind_param('ii', $permission, $id);
      $res_update = $stmt_update->execute();
      $stmt_update->close();
      if(!$res_update){
        //error
      }
    }
  }



