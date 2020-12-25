<?php
  /**
   * tp_MemberTickets, tp_TicketTotal, tp_Reserves, tp_Responses, tp_Promotions, tp_Orders,
   * tp_OrderTypes, tp_Permissions の各テーブルを、存在していなかったら作成し、存在していたら空にする。
   * その後、必要なタプルを挿入する
   * 外部キーを無効にして行わないと、truncateするときに弾かれる。
   */

  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/kleines-mypage/TicketSystems/config/config.php');
  require_once(ROOT."/Common/dbconnect.php");

  //各テーブルが存在していなかったら作成、存在してたら中身を空にする
  $status = ""; //エラー表示用
  if($mysqli->query("SET foreign_key_checks = 0")){ //外部キーのチェックを無効にする
    $status.="SucForeignKeySet0 ";
  }else{
    $status.="FailForeignKeySet0 ";
  }

  $tables = [ /* 作成するテーブル一覧 */
    "tp_MemberTickets",
    "tp_TicketTotal",
    "tp_Reserves",
    "tp_Responses",
    "tp_Promotions",
    "tp_Orders",
    "tp_OrderTypes",
    "tp_Permissions"
  ];

  foreach($tables as $table){
    //テーブルがあるかどうかを調べる
    $q_exist = "SHOW TABLES LIKE '$table'";
    $result = $mysqli->query($q_exist);
    if($result!=NULL && $result->num_rows==1){
      //存在した場合、空にする
      $q_truncate = "TRUNCATE TABLE $table";
      if($mysqli->query($q_truncate)){  //for test
        $status .= "SucTrunc-$table ";
      }else{
        $status .= "FailTrunc-$table ";
      }
    }else{
      //存在しなかった場合、指定のフォーマットでテーブルを作成
      $result_create = createTable($table, $mysqli);
      if($result_create){ //for test
        $status .= "SucCreate-$table ";
      }else{
        $status .= "FailCreate-$table ";
      }
    }
    //挿入するものがある場合、挿入
    $result_insert = insertInitTuples($table, $mysqli);
    if($result_insert){ //for test
      $status .= "SucInsert-$table ";
    }else{
      $status .= "FailInsert-$table ";
    }
    //close(変数を使い回すのでこれをしないとエラーが起こる)
    if($result!=NULL){
      $result->close();
    }
    echo $status+"\n";
  }

  //外部キーのチェックを有効にする
  if($mysqli->query("SET foreign_key_checks = 1")){
    $status.="SucFrgnKeySet1";
  }else{
    $status.="-FailFrgnKeySet1";
    echo "<!--$mysqli->error-->";
      exit();
  }
  
  //関数群
  /**
   * 指定のフォーマットでDBにテーブルを作成する関数
   * @param table_name 作成するテーブルの名前のString
   * @param mysqli 作成するDBのmysqliオブジェクト
   * @return true if succeed or false if not.
   */
  function createTable($table_name, $mysqli){
    $q_create = "";
    $q_key = "";
    $q_constraint = "";
    if(strcmp($table_name,"tp_MemberTickets")==0){
      $q_create = 
      "CREATE TABLE tp_MemberTickets (
      personID int(5) UNSIGNED ZEROFILL NOT NULL,
      have int(11) NOT NULL DEFAULT '0',
      sold int(11) NOT NULL DEFAULT '0') 
      ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_MemberTickets`
      ADD KEY `personID` (`personID`)";
      $q_constraint = 
      "ALTER TABLE `tp_MemberTickets` ADD CONSTRAINT `tp_MemberTickets_ibfk_1`
      FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";

    }else if(strcmp($table_name,"tp_TicketTotal")==0){
      $q_create = 
      "CREATE TABLE `tp_TicketTotal` (
      `ticketTypeCode` int(11) NOT NULL,
      `ticketTypeValue` varchar(50) NOT NULL,
      `amount` int(11) NOT NULL DEFAULT '0'
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key =
      "ALTER TABLE `tp_TicketTotal` ADD PRIMARY KEY (`ticketTypeCode`)";
      $q_constraint =
      "ALTER TABLE `tp_TicketTotal` MODIFY `ticketTypeCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";

    }else if(strcmp($table_name,"tp_Reserves")==0){
      $q_create = 
      "CREATE TABLE `tp_Reserves` (
      `orderID` int(11) NOT NULL,
      `lastName` varchar(30) NOT NULL,
      `firstName` varchar(30) DEFAULT NULL,
      `lastNameKana` varchar(30) NOT NULL,
      `firstNameKana` varchar(30) DEFAULT NULL,
      `price` int(11) NOT NULL,
      `visitFlag` tinyint(1) NOT NULL DEFAULT '0'
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Reserves` ADD KEY `orderID` (`orderID`)";
      $q_constraint = 
      "ALTER TABLE `tp_Reserves` ADD CONSTRAINT `tp_Reserves_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `tp_Orders` (`orderID`)";

    }else if(strcmp($table_name,"tp_Responses")==0){
      $q_create = 
      "CREATE TABLE `tp_Responses` (
      `responseID` int(11) NOT NULL AUTO_INCREMENT,
      `orderID` int(5) NOT NULL,
      `personID` int(5) UNSIGNED ZEROFILL NOT NULL,
      `amount` int(11) NOT NULL,
      `responseTime` datetime DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Responses`
      ADD PRIMARY KEY (`responseID`),
      ADD KEY `orderID` (`orderID`),
      ADD KEY `personID` (`personID`)";
      $q_constraint = 
      "ALTER TABLE `tp_Responses`
      ADD CONSTRAINT `tp_Responses_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `tp_Orders` (`orderID`),
      ADD CONSTRAINT `tp_Responses_ibfk_2` FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";

    }else if(strcmp($table_name,"tp_Promotions")==0){
      $q_create = 
      "CREATE TABLE `tp_Promotions` (
      `orderID` int(11) NOT NULL,
      `groupName` varchar(50) NOT NULL,
      `date` date DEFAULT NULL,
      `finishFlag` tinyint(1) NOT NULL DEFAULT '0',
      `finishTime` datetime DEFAULT NULL,
      `actualAmount` int(11) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Promotions` ADD KEY `orderID` (`orderID`)";
      $q_constraint = 
      "ALTER TABLE `tp_Promotions` ADD CONSTRAINT `tp_Promotions_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `tp_Orders` (`orderID`)";

    }else if(strcmp($table_name,"tp_Orders")==0){
      $q_create = 
      "CREATE TABLE `tp_Orders` (
      `orderID` int(11) NOT NULL AUTO_INCREMENT,
      `personID` int(5) UNSIGNED ZEROFILL NOT NULL,
      `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL,
      `amount` int(11) NOT NULL,
      `orderTime` datetime NOT NULL,
      `response` int(11) NOT NULL DEFAULT '0',
      `finishFlag` tinyint(1) NOT NULL DEFAULT '0',
      `finishTime` datetime DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Orders`
      ADD PRIMARY KEY (`orderID`),
      ADD KEY `orderTypeID` (`orderTypeID`),
      ADD KEY `personID` (`personID`)";
      $q_constraint = 
      "ALTER TABLE `tp_Orders`
      ADD CONSTRAINT `tp_Orders_ibfk_1` FOREIGN KEY (`orderTypeID`) REFERENCES `tp_OrderTypes` (`orderTypeID`),
      ADD CONSTRAINT `tp_Orders_ibfk_2` FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";

    }else if(strcmp($table_name,"tp_OrderTypes")==0){
      $q_create = 
      "CREATE TABLE `tp_OrderTypes` (
      `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL,
      `orderTypeName` varchar(50) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key =
      "ALTER TABLE `tp_OrderTypes` ADD PRIMARY KEY (`orderTypeID`) USING BTREE";
      $q_constraint = 
      "ALTER TABLE `tp_OrderTypes` MODIFY `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";

    }else if(strcmp($table_name,"tp_Permissions")==0){
      $q_create = 
      "CREATE TABLE `tp_Permissions` (
      `personID` int(5) UNSIGNED ZEROFILL NOT NULL,
      `permission` int(10) UNSIGNED NOT NULL DEFAULT '0'
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Permissions` ADD KEY `personID` (`personID`)";
      $q_constraint = 
      "ALTER TABLE `tp_Permissions` ADD CONSTRAINT `tp_Permissions_ibfk_1` FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";
    }else{
      //テーブル名が間違っている場合
      return false;
    }
    $isComplete = $mysqli->query($q_create) && $mysqli->query($q_key) && $mysqli->query($q_constraint);
    return $isComplete;
  }

  /**
   * テーブルに最初から挿入するものがある場合のみ挿入する関数
   * @param table_name 作成するテーブルの名前のString
   * @param mysqli 作成するDBのmysqliオブジェクト
   * @return true if succeed or false if not.
   */
  function insertInitTuples($table_name, $mysqli){
    if(strcmp($table_name,"tp_MemberTickets")==0){
      //membersから全員分の名前を挿入
      $q_insert = "INSERT INTO tp_MemberTickets (personID) SELECT personID FROM members WHERE members.status = 0";
    }else if(strcmp($table_name,"tp_TicketTotal")==0){
      return true;
    }else if(strcmp($table_name,"tp_Reserves")==0){
      return true;
    }else if(strcmp($table_name,"tp_Responses")==0){
      return true;
    }else if(strcmp($table_name,"tp_Promotions")==0){
      return true;
    }else if(strcmp($table_name,"tp_Orders")==0){
      return true;
    }else if(strcmp($table_name,"tp_OrderTypes")==0){
      //オーダー種別を挿入
      $q_insert =
      "INSERT INTO `tp_OrderTypes` (`orderTypeID`, `orderTypeName`) VALUES
      (001, 'request'),
      (002, 'sold'),
      (003, 'want_return'),
      (004, 'want_promotion'),
      (005, 'sold_with_reserve'),
      (006, 'cancel'),
      (007, 'finish_promotion'),
      (008, 'transfer_receive'),
      (009, 'transfer_give'),
      (010, 'cancel_reserve')";
    }else if(strcmp($table_name,"tp_Permissions")==0){
      //マスター権限を持つ活動中の団員を挿入
      $q_insert = 
      "INSERT INTO tp_Permissions (personID,permission) 
      SELECT personID,admin FROM members WHERE members.admin = 1 AND members.status = 0";
    }else{
      //テーブル名が間違っている場合
      return false;
    }
    $isComplete = $mysqli->query($q_insert);
    return $isComplete;
  }



