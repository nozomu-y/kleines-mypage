<?php
  /**
   * permissionの値によってアクセス制限を行う関数
   * 権限が足りない場合は、everyone/index.phpにリダイレクトする
   * permissionのテーブルが存在しない(初期設定前)の場合、secret/backdoorにリダイレクトし、初期設定を促す
   * @param max_permission そのページにアクセス可能な権限の最大値
   * @param id アクセスする人のid。実際のページではUSERから取得
   * @param mysqli mysqliオブジェクト
   */
  function accessFilter($max_permission, $id, $mysqli){
    if(!isInitialized($mysqli)){
      //初期設定画面へ
      $mysqli->close();
      header("Location: ". TP_SERVER . "/pages/secret/backdoor/index.php");
      exit();
    }
    //テーブルがある場合、アクセス者のpermissionを取得
    $permission = getPermission($id, $mysqli);
    //指定されたmax_permissionと大小比較し、権限がない場合はエラーメッセージを設定してリダイレクト
    if($max_permission < $permission){
      $_SESSION['tp_status'] = "not-permittied";
      $mysqli->close();
      header("Location: ".TP_SERVER."/pages/everyone/index.php");
      exit();
    }
  }

  /**
   * チーフ専用ページのフィルター
   * 初期設定前の場合、または権限が足りない場合は、パスワード入力ページにリダイレクト
   */
  function secretFilter($max_permission, $id, $mysqli){
    //パスワード入力によって権限が足りている場合
    if(isset($_SESSION['tp_secret']) && $_SESSION['tp_secret'] === "allowed"){
      return;
    }
    //初期設定前の場合、または初期設定済みだが権限が足りてない時
    if(!isInitialized($mysqli) || ($max_permission < getPermission($id, $mysqli))){
      $mysqli->close();
      header("Location: ". TP_SERVER . "/pages/secret/backdoor/index.php");
      exit();
    }
  }

  /**
   * 初期設定済みかを確認する関数
   */
  function isInitialized($mysqli){
    //tp_Permissionテーブルがあるかを確認し、初期設定後かを判定
    $q_exist = "SHOW TABLES LIKE 'tp_Permissions'"; //完全一致でテーブルを検索
    $result = $mysqli->query($q_exist);
    if($result==NULL || $result->num_rows==0){
      return false;
    }else{
      return true;
    }
  }

  /**
   * idからアクセス者のpermissionを返すメソッド
   * ※注意：使用前にテーブルが存在するかどうかを確認してください
   */
  function getPermission($id, $mysqli){
    $stmt_select = $mysqli->prepare('SELECT permission FROM tp_Permissions WHERE id = ?');
    $stmt_select->bind_param('i',$id);
    $stmt_select->execute();
    $stmt_select->bind_result($permission);
    $result = $stmt_select->fetch();
    if($result){
      return $permission;
    }else{
      return NO_PERM_NUM;
    }
  }



