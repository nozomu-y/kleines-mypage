<?php
  function linkHandle($finishFlag, $deleteFlag, $orderTypeName, $orderID){
    if($deleteFlag == 1) return;
    switch($orderTypeName){
      case "request":
      case "want_return":
        //未処理のやつを消去する
        if($finishFlag == 0){
          echo("<button class='btn btn-sm btn-danger js-modal-open' type='button' data-target='confirmModal'>取消</button>");
        }
        break;
      case "sold_with_reserve":
        echo("<a class='btn btn-sm btn-primary' href='editReserveForm.php?orderID=$orderID'>預かり編集</a>");
        break;
      }
    }