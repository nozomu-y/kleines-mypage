<?php
/******
・tp_MemberTickets,tp_TicketTotal,tp_Reserves,tp_Responses,tp_Promotions,tp_Ordersを<br>
  存在していなかったら作成<br>
  存在していたら、中身を空にする<br>
・membersから、tp_MemberTicketsに入っていない団員のIDを抽出して挿入(have,soldは0)<br>
・全メンバーについて、have,soldの値を0にセット<br>
 */
  //ini_set('display_errors',1);
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  startSecretSession();
  $mysqli = dbconnect();


  //各テーブルが存在していなかったら作成、存在してたら中身を空にする
  $status = ""; //エラー表示用
  //外部キーのチェックを無効にする
  if($mysqli->query("SET foreign_key_checks = 0")){
    $status.="SucFrgnKeySet0";
  }else{
    $status.="FailFrgnKeySet0";
  }
  //tp_MemberTickets
  $q1 = "SHOW TABLES LIKE 'tp_MemberTickets'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE tp_MemberTickets (
      personID int(5) UNSIGNED ZEROFILL NOT NULL,
      have int(11) NOT NULL DEFAULT '0',
      sold int(11) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q3 = "ALTER TABLE `tp_MemberTickets`
    ADD KEY `personID` (`personID`)";
    $q4 = "ALTER TABLE `tp_MemberTickets` ADD CONSTRAINT `tp_MemberTickets_ibfk_1` FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)){
      $status .= "-SucCreMemTic";
    }else{
      $status .= "-FailCreMemTic";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在：空にする
    $q2 = "TRUNCATE TABLE tp_MemberTickets";
    if($mysqli->query($q2)){
      $status.="-SucTrncMemTic";
    }else{
      $status.="-FailTrncMemTic";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }
  if($result!=NULL){
    $result->close();
  }

  //tp_TicketTotal
  $q1 = "SHOW TABLES LIKE 'tp_TicketTotal'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE `tp_TicketTotal` (
      `ticketTypeCode` int(11) NOT NULL,
      `ticketTypeValue` varchar(50) NOT NULL,
      `amount` int(11) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q3 = "ALTER TABLE `tp_TicketTotal` ADD PRIMARY KEY (`ticketTypeCode`)";
    $q4 = "ALTER TABLE `tp_TicketTotal` MODIFY `ticketTypeCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)){
      $status .= "-SucCreTicTot";
    }else{
      $status .= "-FailCreTicTot";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在：空にする
    $q2 = "TRUNCATE TABLE tp_TicketTotal";
    if($mysqli->query($q2)){
      $status.="-SucTrncTicTot";
    }else{
      $status.="-FailTrncTicTot";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }
  if($result!=NULL){
    $result->free();
  }
  

  //tp_OrderTypes
  $q1 = "SHOW TABLES LIKE 'tp_OrderTypes'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE `tp_OrderTypes` (
      `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL,
      `orderTypeName` varchar(50) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q3 = "INSERT INTO `tp_OrderTypes` (`orderTypeID`, `orderTypeName`) VALUES
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
    $q4="ALTER TABLE `tp_OrderTypes` ADD PRIMARY KEY (`orderTypeID`) USING BTREE";
    $q5 = "ALTER TABLE `tp_OrderTypes` MODIFY `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11";
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)&&$mysqli->query($q5)){
      $status .= "-SucCreOdrT";
    }else{
      $status .= "-FailCreOdrT";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在したら、空にしてorderTypeIDを挿入
    $q2 = "TRUNCATE TABLE tp_OrderTypes";
    $q3 = "INSERT INTO `tp_OrderTypes` (`orderTypeID`, `orderTypeName`) VALUES
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
    if($mysqli->query($q2)&&$mysqli->query($q3)){
      $status.="-SucTrnc&InsOdrT";
    }else{
      $status.="-FailTrncOdrT";
      echo $mysqli->error;
      exit();
    }
  }
  //テーブルが存在したらそのまま
  if($result!=NULL){
    $result->free();
  }

  //tp_Orders
  $q1 = "SHOW TABLES LIKE 'tp_Orders'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE `tp_Orders` (
      `orderID` int(11) NOT NULL,
      `personID` int(5) UNSIGNED ZEROFILL NOT NULL,
      `orderTypeID` int(3) UNSIGNED ZEROFILL NOT NULL,
      `amount` int(11) NOT NULL,
      `orderTime` datetime NOT NULL,
      `response` int(11) NOT NULL DEFAULT '0',
      `finishFlag` tinyint(1) NOT NULL DEFAULT '0',
      `finishTime` datetime DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q3 = "ALTER TABLE `tp_Orders`
    ADD PRIMARY KEY (`orderID`),
    ADD KEY `orderTypeID` (`orderTypeID`),
    ADD KEY `personID` (`personID`)";
    $q4 = "ALTER TABLE `tp_Orders` MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
    $q5 = "ALTER TABLE `tp_Orders`
    ADD CONSTRAINT `tp_Orders_ibfk_1` FOREIGN KEY (`orderTypeID`) REFERENCES `tp_OrderTypes` (`orderTypeID`),
    ADD CONSTRAINT `tp_Orders_ibfk_2` FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)&&$mysqli->query($q5)){
      $status .= "-SucCreOdrs";
    }else{
      $status .= "-FailCreOdrs";
      echo $mysqli->error;
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在：空にする
    $q2 = "TRUNCATE TABLE tp_Orders";
    if($mysqli->query($q2)){
      $status.="-SucTrncOdrs";
    }else{
      $status.="-FailTrncOdrs";
      echo $mysqli->error;
      exit();
    }
  }
  if($result!=NULL){
    $result->free();
  }

  //tp_Reserves
  $q1 = "SHOW TABLES LIKE 'tp_Reserves'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE `tp_Reserves` (
      `orderID` int(11) NOT NULL,
      `lastName` varchar(30) NOT NULL,
      `firstName` varchar(30) DEFAULT NULL,
      `lastNameKana` varchar(30) NOT NULL,
      `firstNameKana` varchar(30) DEFAULT NULL,
      `price` int(11) NOT NULL,
      `visitFlag` tinyint(1) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q3 = "ALTER TABLE `tp_Reserves` ADD KEY `orderID` (`orderID`)";
    $q4 = "ALTER TABLE `tp_Reserves` ADD CONSTRAINT `tp_Reserves_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `tp_Orders` (`orderID`)";
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)){
      $status .= "-SucCreRsvs";
    }else{
      $status .= "-FailCreRsvs";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在：空にする
    $q2 = "TRUNCATE TABLE tp_Reserves";
    if($mysqli->query($q2)){
      $status.="-SucTrncRsvs";
    }else{
      $status.="-FailTrncRsvs";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }
  if($result!=NULL){
    $result->free();
  }

  //tp_Responses
  $q1 = "SHOW TABLES LIKE 'tp_Responses'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE `tp_Responses` (
      `responseID` int(11) NOT NULL,
      `orderID` int(5) NOT NULL,
      `personID` int(5) UNSIGNED ZEROFILL NOT NULL,
      `amount` int(11) NOT NULL,
      `responseTime` datetime DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q3 = "ALTER TABLE `tp_Responses`
  ADD PRIMARY KEY (`responseID`),
  ADD KEY `orderID` (`orderID`),
  ADD KEY `personID` (`personID`)";
  $q4 = "ALTER TABLE `tp_Responses` MODIFY `responseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
  $q5 = "ALTER TABLE `tp_Responses`
  ADD CONSTRAINT `tp_Responses_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `tp_Orders` (`orderID`),
  ADD CONSTRAINT `tp_Responses_ibfk_2` FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)&&$mysqli->query($q5)){
      $status .= "-SucCreRsps";
    }else{
      $status .= "-FailCreRsps";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在：空にする
    $q2 = "TRUNCATE TABLE tp_Responses";
    if($mysqli->query($q2)){
      $status.="-SucTrncRsps";
    }else{
      $status.="-FailTrncRsps";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }
  if($result!=NULL){
    $result->free();
  }

  //tp_Promotions
  $q1 = "SHOW TABLES LIKE 'tp_Promotions'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE `tp_Promotions` (
  `orderID` int(11) NOT NULL,
  `groupName` varchar(50) NOT NULL,
  `date` date DEFAULT NULL,
  `finishFlag` tinyint(1) NOT NULL DEFAULT '0',
  `finishTime` datetime DEFAULT NULL,
  `actualAmount` int(11) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  $q3 = "ALTER TABLE `tp_Promotions` ADD KEY `orderID` (`orderID`)";
  $q4 = "ALTER TABLE `tp_Promotions` ADD CONSTRAINT `tp_Promotions_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `tp_Orders` (`orderID`)";
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)){
      $status .= "-SucCrePrms";
    }else{
      $status .= "-FailCrePrms";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在：空にする
    $q2 = "TRUNCATE TABLE tp_Promotions";
    if($mysqli->query($q2)){
      $status.="-SucTrncPrms";
    }else{
      $status.="-FailTrncPrms";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }
  if($result!=NULL){
    $result->free();
  }

  //tp_Permissions
  $q1 = "SHOW TABLES LIKE 'tp_Permissions'"; //完全一致でテーブルを検索
  $result = $mysqli->query($q1);
  if($result==NULL || $result->num_rows==0){
    //テーブルが存在しない：作成
    $q2 =  "CREATE TABLE `tp_Permissions` (
      `personID` int(5) UNSIGNED ZEROFILL NOT NULL,
      `permission` int(10) UNSIGNED NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q3 = "ALTER TABLE `tp_Permissions` ADD KEY `personID` (`personID`)";
    $q4 = "ALTER TABLE `tp_Permissions` ADD CONSTRAINT `tp_Permissions_ibfk_1` FOREIGN KEY (`personID`) REFERENCES `members` (`personID`)";
    $q5 = "INSERT INTO tp_Permissions (personID,permission) SELECT personID,admin FROM members WHERE members.admin = 1";  //マスター権限を持つ人をそのまま入れる
    if($mysqli->query($q2)&&$mysqli->query($q3)&&$mysqli->query($q4)&&$mysqli->query($q5)){
      $status .= "-SucCrePrmss";
    }else{
      $status .= "-FailCrePrmss";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }else if($result->num_rows==1){
    //テーブルが存在：空にして、マスター権限所持者のみ入れる
    $q2 = "TRUNCATE TABLE tp_Permissions";
    $q5 = "INSERT INTO tp_Permissions (personID,permission) SELECT personID,admin FROM members WHERE members.admin = 1";  //マスター権限を持つ人をそのまま入れる
    if($mysqli->query($q2)&&$mysqli->query($q5)){
      $status.="-SucTrncAndInsPrmss";
    }else{
      $status.="-FailTrncAndInsPrmss";
      echo "<!--$status--$mysqli->error-->";
      exit();
    }
  }
  if($result!=NULL){
    $result->close();
    //$mysqli->next_result();
  }

  //外部キーのチェックを有効にする
  if($mysqli->query("SET foreign_key_checks = 1")){
    $status.="-SucFrgnKeySet1";
  }else{
    $status.="-FailFrgnKeySet1";
    echo "<!--$mysqli->error-->";
      exit();
  }
  echo "<!--$status-->";

  //tp_MemberTicketsにまだ入っていないpersonIDだけを、membersから抽出して挿入するSQL
  $sql = "INSERT INTO tp_MemberTickets (personID) SELECT personID FROM members WHERE members.status = 0 AND NOT EXISTS (SELECT personID FROM tp_MemberTickets WHERE members.personID = tp_MemberTickets.personID)";
  $result = $mysqli->query($sql);
  if(!$result){
    header("Location: ".SERVER."/view/private/failureProcess.php?failure=cannotInsert&$status");  //エラー画面表示のページにリダイレクト
    exit;
  }
  if(!$result){
    echo "<!-- fail insert : code : $mysqli->error -->";
  }
  dbclose($mysqli);
?>