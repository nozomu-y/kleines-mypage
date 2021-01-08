<?php
  /**
   * TODO:
   * OrdersがfinishFlag===1じゃなかったらやらない
   * 日付のチェック
   * 最初の人を自動で本人に変更する
   */
  require_once TP_ROOT."/include/orders/orderHandler.php";
  $orderID = htmlspecialchars($_GET['orderID']);
  $IDs = $_POST['id'];
  $amount_given = $_POST['amount-given'];
  $amount_self = $_POST['amount-self'];
  $num_person = count($IDs);
  $sum_given = 0; //渉外からもらったチケットから売った枚数
  $sum_all = 0; //情宣で売った枚数

  //渉外からもらった枚数を取得
  $stmt_select = $mysqli->prepare("SELECT amount FROM tp_Orders WHERE orderID = ?");
	$stmt_select->bind_param('i', $orderID);
	$stmt_select->execute();
	$stmt_select->bind_result($amount_given_all);
	$result = $stmt_select->fetch();
  $stmt_select->close();

  for($i=0; $i<$num_person; $i++){
    //人ごとに、soldを登録
    $amount = $amount_given[$i] + $amount_self[$i];
    if($i != 0){
      transferTicket($IDs[0], $IDs[$i], $amount_given[$i], $mysqli);
    }
    insertOrder($IDs[$i], 2, $amount, $mysqli);
    updateTicketAmount($IDs[$i], 2, $amount, $mysqli);
    //合計を算出
    $sum_given += $amount_given[$i];
    $sum_all += $amount;
  }

  //tp_Promotionを更新
  $finishFlag = 1;
  $finishTime = date("Y-m-d H:i:s");
  $stmt_update = $mysqli->prepare(
    "UPDATE tp_Promotions SET finishFlag = ?, finishTime = ?, actualAmount = ? WHERE orderID = ?");
  $stmt_update->bind_param('isii', $finishFlag, $finishTime, $sum_all, $orderID);
  if(!$stmt_update->execute()){
    echo($mysqli->error);
    exit();
  }
  $stmt_update->close();

  //残り枚数を算出
  $amount_rest = $amount_given_all - $sum_given;

  //tp_Ordersにfinish_promotionを登録
  insertOrder($IDs[0], 7, $amount_rest, $mysqli);

