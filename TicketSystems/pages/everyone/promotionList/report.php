<?php
  /**
   * TODO:
   * OrdersがfinishFlag===1じゃなかったらやらない(枚数フィルター)
   * 日付のチェック
   */
  require_once TP_ROOT."/include/orders/orderHandler.php";
  $orderID = htmlspecialchars($_GET['orderID']);
  $id_rep = $_POST['id-rep'];
  $amount_given_rep = $_POST['amount-given-rep'];
  $amount_self_rep = $_POST['amount-self-rep'];
  $id_acc = $_POST['id-acc'];
  $amount_given_acc = $_POST['amount-given-acc'];
  $amount_self_acc = $_POST['amount-self-acc'];
  $num_acc = count($id_acc);
  $sum_given = 0; //渉外からもらったチケットから売った枚数
  $actualAmount = 0; //情宣で売った枚数

  //渉外からもらった枚数を取得
  $stmt_select = $mysqli->prepare("SELECT amount FROM tp_Orders WHERE orderID = ?");
	$stmt_select->bind_param('i', $orderID);
	$stmt_select->execute();
	$stmt_select->bind_result($amount_given_all);
	$result = $stmt_select->fetch();
  $stmt_select->close();

  //同伴者のオーダーを登録
  for($i=0; $i<$num_acc; $i++){
    //人ごとに、soldを登録
    $amount = $amount_given_acc[$i] + $amount_self_acc[$i];
    transferTicket($id_rep, $id_acc[$i], $amount_given_acc[$i], $mysqli);
    insertOrder($id_acc[$i], 2, $amount, $mysqli);
    updateTicketAmount($id_acc[$i], 2, $amount, $mysqli);
    //合計を算出
    $sum_given += $amount_given_acc[$i];
    $actualAmount += $amount;
  }

  //代表者のオーダーを登録
  $amount = $amount_given_rep + $amount_self_rep;
  insertOrder($id_rep, 2, $amount, $mysqli);
  updateTicketAmount($id_rep, 2, $amount, $mysqli);
  $sum_given += $amount_given_rep;
  $actualAmount += $amount;

  //情宣用チケットの残り枚数を算出
  $amount_rest = $amount_given_all - $sum_given;

  //tp_Ordersにfinish_promotionを登録
  insertOrder($id_rep, 7, $amount_rest, $mysqli);

  //tp_Promotionを更新
  $finishFlag = 1;
  $finishTime = date("Y-m-d H:i:s");
  $stmt_update = $mysqli->prepare(
    "UPDATE tp_Promotions SET finishFlag = ?, finishTime = ?, actualAmount = ? WHERE orderID = ?");
  $stmt_update->bind_param('isii', $finishFlag, $finishTime, $actualAmount, $orderID);
  if(!$stmt_update->execute()){
    echo($mysqli->error);
    exit();
  }
  $stmt_update->close();

  

