<?php
  function linkHandle($finishFlag, $orderTypeName, $orderID){
    if($finishFlag == 0){
      //未処理のやつを消去する
      echo("<a class='btn btn-sm btn-danger' href='".$_SERVER['PHP_SELF']."?process=deleteOrder'>取消</a>");
    }else if($finishFlag == 1 && $orderTypeName === "sold_with_reserve"){
      //預かり編集
      echo("<a class='btn btn-sm btn-primary' href='editReserve.php?orderID=$orderID'>預かり編集</a>");
    }
  }