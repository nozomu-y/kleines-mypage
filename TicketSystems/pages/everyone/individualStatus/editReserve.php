<?php
  $orderID = htmlspecialchars($_POST['orderID']);
  $lname = htmlspecialchars($_POST['lname']);
  $fname = htmlspecialchars($_POST['fname']);
  $lnameKana = htmlspecialchars($_POST['lname-kana']);
  $fnameKana = htmlspecialchars($_POST['fname-kana']);
  $price = htmlspecialchars($_POST['price']);

  $stmt_update = $mysqli->prepare(
    "UPDATE tp_Reserves SET lastName = ?, firstName = ?, lastNameKana = ?, firstNameKana = ?, price = ? 
     WHERE orderID = ?");
  $stmt_update->bind_param('ssssii', $lname, $fname, $lnameKana, $fnameKana, $price, $orderID);
  if(!$stmt_update->execute()){
    echo($mysqli->error);
    exit();
  }
  $stmt_update->close();