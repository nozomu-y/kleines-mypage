<?php
  /**
   * 前提：membersテーブルは既に存在していないと動きません
   * tp_MemberTickets, tp_TicketTotal, tp_Reserves, tp_Responses, tp_Promotions, tp_Orders,
   * tp_OrderTypes, tp_Permissions の各テーブルを、存在していなかったら作成し、存在していたら空にする。
   * その後、必要なタプルを挿入する
   * 外部キーを無効にして行わないと、truncateするときに弾かれる。
   * テーブル間に外部キー制約があるので順番にも注意。
   */

  $res_foreign_key = $mysqli->query("SET foreign_key_checks = 0"); //外部キーのチェックを無効にする

  $tables = [ /* 作成するテーブル一覧 */
    "tp_OrderTypes",
    "tp_Orders",
    "tp_MemberTickets",
    "tp_TicketTotal",
    "tp_Reserves",
    "tp_Responses",
    "tp_Promotions",
    "tp_OrderTypes",
    "tp_Permissions"
  ];

  foreach($tables as $table){
    //テーブルがあるかどうかを調べる
    $q_exist = "SHOW TABLES LIKE '$table'";
    $res_show = $mysqli->query($q_exist);
    if($res_show!=NULL && $res_show->num_rows==1){
      //存在した場合、空にする
      $q_truncate = "TRUNCATE TABLE $table";
      $res_trunc = $mysqli->query($q_truncate);
    }else{
      //存在しなかった場合、指定のフォーマットでテーブルを作成
      $res_create = createTable($table, $mysqli);
    }
    //挿入するものがある場合、挿入
    $res_insert = insertInitTuples($table, $mysqli);
    //close(変数を使い回すのでこれをしないとエラーが起こる)
    if($res_show!=NULL){
      $res_show->close();
    }
  }

  //外部キーのチェックを有効にする
  $res_foreign_key = $mysqli->query("SET foreign_key_checks = 1");
  
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
    if($table_name === "tp_MemberTickets"){
      $q_create = 
      "CREATE TABLE tp_MemberTickets (
      id int(5) UNSIGNED ZEROFILL NOT NULL,
      have int(11) NOT NULL DEFAULT '0',
      sold int(11) NOT NULL DEFAULT '0') 
      ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_MemberTickets`
      ADD KEY `id` (`id`)";
      $q_constraint = 
      "ALTER TABLE `tp_MemberTickets` ADD CONSTRAINT `tp_MemberTickets_ibfk_1`
      FOREIGN KEY (`id`) REFERENCES `members` (`id`)";

    }else if($table_name === "tp_TicketTotal"){
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

    }else if($table_name === "tp_Reserves"){
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

    }else if($table_name === "tp_Responses"){
      $q_create = 
      "CREATE TABLE `tp_Responses` (
      `responseID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `orderID` int(5) NOT NULL,
      `id` int(5) UNSIGNED ZEROFILL NOT NULL,
      `amount` int(11) NOT NULL,
      `responseTime` datetime DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Responses`
      ADD KEY `orderID` (`orderID`),
      ADD KEY `id` (`id`)";
      // ADD PRIMARY KEY (`responseID`),
      $q_constraint = 
      "ALTER TABLE `tp_Responses`
      ADD CONSTRAINT `tp_Responses_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `tp_Orders` (`orderID`),
      ADD CONSTRAINT `tp_Responses_ibfk_2` FOREIGN KEY (`id`) REFERENCES `members` (`id`)";

    }else if($table_name === "tp_Promotions"){
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

    }else if($table_name === "tp_Orders"){
      $q_create = 
      "CREATE TABLE `tp_Orders` (
      `orderID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
      `id` int(5) UNSIGNED ZEROFILL NOT NULL,
      `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL,
      `amount` int(11) NOT NULL,
      `orderTime` datetime NOT NULL,
      `response` int(11) NOT NULL DEFAULT '0',
      `finishFlag` tinyint(1) NOT NULL DEFAULT '0',
      `finishTime` datetime DEFAULT NULL,
      `deleteFlag` tinyint(1) NOT NULL DEFAULT '0',
      `deleteTime` datetime DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Orders`
      ADD KEY `orderTypeID` (`orderTypeID`),
      ADD KEY `id` (`id`)";
      // ADD PRIMARY KEY (`orderID`),
      $q_constraint = 
      "ALTER TABLE `tp_Orders`
      ADD CONSTRAINT `tp_Orders_ibfk_1` FOREIGN KEY (`orderTypeID`) REFERENCES `tp_OrderTypes` (`orderTypeID`),
      ADD CONSTRAINT `tp_Orders_ibfk_2` FOREIGN KEY (`id`) REFERENCES `members` (`id`)";

    }else if($table_name === "tp_OrderTypes"){
      $q_create = 
      "CREATE TABLE `tp_OrderTypes` (
      `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL,
      `orderTypeName` varchar(50) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key =
      "ALTER TABLE `tp_OrderTypes` ADD PRIMARY KEY (`orderTypeID`) USING BTREE";
      $q_constraint = 
      "ALTER TABLE `tp_OrderTypes` MODIFY `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";

    }else if($table_name === "tp_Permissions"){
      $q_create = 
      "CREATE TABLE `tp_Permissions` (
      `id` int(5) UNSIGNED ZEROFILL NOT NULL,
      `permission` int(10) UNSIGNED NOT NULL DEFAULT '0'
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_Permissions` ADD KEY `id` (`id`)";
      $q_constraint = 
      "ALTER TABLE `tp_Permissions` ADD CONSTRAINT `tp_Permissions_ibfk_1` FOREIGN KEY (`id`) REFERENCES `members` (`id`)";
    /*
    }else if($table_name === "tp_TestMembers"){
      $q_create = 
      "CREATE TABLE `tp_TestMembers` (
        `id` int(5) UNSIGNED ZEROFILL NOT NULL,
        `email` varchar(256) CHARACTER SET utf8mb4 DEFAULT NULL,
        `password` varchar(256) CHARACTER SET utf8mb4 DEFAULT NULL,
        `last_name` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
        `first_name` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
        `kana` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
        `grade` int(2) DEFAULT NULL,
        `part` varchar(1) CHARACTER SET utf8mb4 DEFAULT NULL,
        `token` varchar(256) CHARACTER SET utf8mb4 DEFAULT NULL,
        `validation_time` datetime DEFAULT NULL,
        `login_failure` int(2) NOT NULL DEFAULT '0',
        `admin` int(1) DEFAULT NULL,
        `status` int(1) NOT NULL DEFAULT '0'
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      $q_key = 
      "ALTER TABLE `tp_TestMembers` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`)";
      $q_constraint = 
      "ALTER TABLE `tp_TestMembers`
      MODIFY `id` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
      */
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
    if($table_name === "tp_MemberTickets"){
      //membersから全員分の名前を挿入
      $q_insert = "INSERT INTO tp_MemberTickets (id) SELECT id FROM members WHERE members.status = 0";
    }else if($table_name === "tp_TicketTotal"){
      //必ず必要な4つを挿入する
      $q_insert = 
      "INSERT INTO `tp_TicketTotal` (`ticketTypeCode`, `ticketTypeValue`, `amount`) VALUES
      (1, '渉外所持', 0),
      (2, '回収済み預かりチケット', 0),
      (3, '団員所持', 0),
      (4, '団員販売済(情宣含む)', 0)";
    }else if($table_name === "tp_Reserves"){
      return true;
    }else if($table_name === "tp_Responses"){
      return true;
    }else if($table_name === "tp_Promotions"){
      return true;
    }else if($table_name === "tp_Orders"){
      return true;
    }else if($table_name === "tp_OrderTypes"){
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
      (010, 'cancel_reserve'),
      (011, 'cancel_promotion')";
    }else if($table_name === "tp_Permissions"){
      //全ての活動中の団員を挿入
      $q_insert = 
      "INSERT INTO tp_Permissions (id, permission) 
      SELECT id, CASE admin WHEN 1 then 1 ELSE ".NO_PERM_NUM." end as permission 
      FROM members WHERE members.status = 0";
      /*
    }else if($table_name === "tp_TestMembers"){
      $q_insert = 
      "INSERT INTO `tp_TestMembers` (`id`, `email`, `password`, `last_name`, `first_name`, `kana`, `grade`, `part`, `token`, `validation_time`, `login_failure`, `admin`, `status`) VALUES
      (00001, '1111@mail', '\$2y\$10\$8xDzzqXZBjDkZnm2.BA8..cRn64HMBXHtY5YbdszXzuaP2cqZ2nAS', '渉外', 'チーフ子', NULL, 18, 'S', NULL, NULL, 0, NULL, 0),
      (00002, '2222@mail', '\$2y\$10\$v.OKPm9dQtFsfof9GqbpquHE9h7ufFrkE5RGhpKWPP6DAYP1e5RtO', '渉外', '花子', NULL, 18, 'A', NULL, NULL, 0, NULL, 0),
      (00003, '3333@mail', '\$2y\$10\$CXo99ORVAESp8.8JuQe13OtsvShcfGIK1IBVCYQ4NRzd3Td7q52Um', '団員', '次郎', NULL, 18, 'T', NULL, NULL, 0, NULL, 0),
      (00004, '4444@mail', '\$2y\$10\$nLBjzQVXGOjBcxCHG4hKmu6seGxYtOlwQfAzO4e/j7z46tF7zd3GS', '団員', '太郎', NULL, 18, 'B', NULL, NULL, 0, NULL, 0),
      (00005, '14b@mail', '\$2y\$10\$s2X2xjlcLvbLhCvYeMpf.efOzRv4TQp2r7AdNF2x5b.l9rBkGEp8G', '安桜', 'ブンブン', NULL, 14, 'B', NULL, NULL, 0, NULL, 0),
      (00006, '16web@mail', '\$2y\$10\$niR34xTXFh5g6rLiQ/pf.ue5qbBqMr1BUFczPtr3/xXguXk8jo4nS', 'ウェブ管', 'ボス太郎', NULL, 16, 'B', NULL, NULL, 0, 1, 0),
      (00007, '16t@mail', '\$2y\$10\$Ba72.o9o4YPy.I.m67H2NeandS/j1dlNBnt7m9QrjEcZ8rAK/pSz.', 'マイン', 'クラフト', NULL, 16, 'T', NULL, NULL, 0, NULL, 0),
      (00008, '17s@mail', '\$2y\$10\$BSNv5j59aDPMK0KAYqaiZuXsO8FzRPYP/KoKbffaEWdeC7dowruze', '元渉外', 'チーフ子', NULL, 17, 'S', NULL, NULL, 0, NULL, 0),
      (00009, 'front@mail', '\$2y\$10\$.HTbAzR20.s4JEFtl7I04eUuBLY0ZJiyHe.tgu.OFufB8vmSLLdmi', 'フロント', 'チーフ子', NULL, 18, 'S', NULL, NULL, 0, NULL, 0),
      (00010, '16front@mail', '\$2y\$10\$71D/RuHD9AgmuTc2IfUI2eoUfpkG5l7VxdigDmsMDP42hE7JSSTY.', 'フロント', '雑用', NULL, 16, 'B', NULL, NULL, 0, 1, 0),
      (00011, '18web@mail', '\$2y\$10\$mhM3eLnVPg8ZmYda1TWXI.6GEu5JuhjprxROWsb9YNTZVX4Ks6jVu', 'ウェブ管', 'エース', NULL, 18, 'T', NULL, NULL, 0, 1, 0),
      (00012, 'stmn@mail', '\$2y\$10\$wjwt/UgvFyMZMC8n/eYGC.lgmccaSTb7CKBFEWh3/b7x2c//52IKK', 'ステマネ', '花子', NULL, 18, 'A', NULL, NULL, 0, NULL, 0),
      (00013, 'sfukui@mail', '\$2y\$10\$tcxNjfSqW.UWhxKR3RyDveCIHi0AXkDTQ424pnR4lOqSR1tsqrvWC', 'ソプラノ', 'ふくい', NULL, 18, 'S', NULL, NULL, 0, NULL, 0),
      (00014, 'afukui@mail', '\$2y\$10\$jphydTNDzHt.5TuCDMsMAuKM4PsF7wTkiOrjggzRpu5zbfD3rHrBq', 'アルト', 'ふくい', NULL, 18, 'A', NULL, NULL, 0, NULL, 0),
      (00015, 'tfukui@mail', '\$2y\$10\$5BGOUzZGME5DofpEXZMt/OA03Sscwld8G1UnQm4UZVtNUGJfopnO2', 'テナー', 'ふくい', NULL, 18, 'T', NULL, NULL, 0, NULL, 0),
      (00016, 'bfukui@mail', '\$2y\$10\$lq.7f9OLWYnsZWBKPbRH..P0CiTG3exvsFdjG//Cx73hR36ZhHXfy', 'ベース', 'ふくい', NULL, 18, 'B', NULL, NULL, 0, NULL, 0),
      (00017, 'sshogai@mail', '\$2y\$10\$Ikdi0OXPGm66CphE99W.g.k1h030Go4rN13jNbOCSbSk/fSkmZx5S', '渉外', 'ソプ子', NULL, 19, 'S', NULL, NULL, 0, NULL, 0),
      (00018, 'ashogai@mail', '\$2y\$10\$A6JXnn1tHHmV1joOvoSg4uhYYEjr7pM0J11bxIILrI5IIFyBPxGpy', '渉外', 'アル子', NULL, 19, 'A', NULL, NULL, 0, NULL, 0),
      (00019, 'tshogai@mail', '\$2y\$10\$mHNkeHVHHuiNiCOGEH33iuyNwo5NxUhrTCY95sBWKUaS2GgQ4QVIi', '渉外', 'テナ太郎', NULL, 19, 'T', NULL, NULL, 0, NULL, 0),
      (00020, 'bshogai@mail', '\$2y\$10\$tpM03fD4cTUwskoiB9CRYuei25DADsO/q0uYZOvg9qD4qpSzZfpx2', '渉外', 'バス太郎', NULL, 19, 'B', NULL, NULL, 0, NULL, 0),
      (00021, '20s@mail', '\$2y\$10\$4mhiyJuB8bdTCYHGagbcxOD0Gwh8BOzm/wXHY7iS4hceB1F5j9.BS', '新入', 'えすこ', NULL, 20, 'S', NULL, NULL, 0, NULL, 0),
      (00022, '20a@mail', '\$2y\$10\$UDp7kdxEjv3o28ZiZkIFjOpgz7tOUvwHB1kfqlXZeh3wwqqFdK2ki', '新入', 'えいこ', NULL, 20, 'A', NULL, NULL, 0, NULL, 0),
      (00023, '20t@mail', '\$2y\$10\$J4H5agTnDGoE9d0mv6j1peYpbvASlKhGEIGJsgMJaefZxkGwFijZ6', '新入', 'ていた', NULL, 20, 'T', NULL, NULL, 0, NULL, 0),
      (00024, '20b@mail', '\$2y\$10\$PG9XCjDGdsQVA1qU8EXk2ud6yzGxansfLzz4P0KVNUtKG.OxrpnjK', '新入', 'びいた', NULL, 20, 'B', NULL, NULL, 0, NULL, 0),
      (00025, '20front@mail', '\$2y\$10\$wU9fsxza6CSav45zZgS8W.dYMKcGerR.cQr/b739n.2Eu2QnSsLe6', 'フロント', 'サブ子', NULL, 20, 'S', NULL, NULL, 0, NULL, 0)";
    */
    }else{
      //テーブル名が間違っている場合
      return false;
    }
    $isComplete = $mysqli->query($q_insert);
    return $isComplete;
  }



