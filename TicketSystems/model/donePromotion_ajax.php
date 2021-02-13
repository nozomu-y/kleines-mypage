<?php
  ini_set('display_errors',1);
  //Ajax以外からのアクセスを遮断
  $request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
      ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
  if($request !== 'xmlhttprequest') exit;

  //DB接続
  require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
  require_once(ROOT.'/controller/functions.php');
  $mysqli = dbconnect();
  //echo "接続エラー".$mysqli->connect_errno;
 
  //SELECT
  $stmt = $mysqli->prepare("SELECT first_name,last_name FROM members WHERE part = ? AND grade = ?");
  //memo 自分を含まないようにする
  /*if($stmt==FALSE){
    echo "  stmt=FALSE : $mysqli->error ";
  }else{
    echo "  stmt=NOT FALSE";
  }*/
  $grade = $_POST['grade'];
  $part = $_POST['part'];
  $stmt->bind_param('si',$part,$grade);
  $stmt->execute();
  $stmt->bind_result($firstName,$lastName);
  $names = array();
  while($result = $stmt->fetch()){
    $names[] = $lastName." ".$firstName;
  }
  $stmt->close();
  dbclose($mysqli);
  //結果をjson形式で返す
  header('Content-Type: application/json');
  echo json_encode($names);

?>