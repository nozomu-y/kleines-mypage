$(function(){
  calcAmount(); //ロード時に呼び出す

  //amountのフォームが変更されるたびに呼び出す
  $("input[class*='js-valid-amount']").blur(function(event){
    calcAmount();
  });

  /**
   * 渉外所持の枚数(ticketTypeAmount[0])を自動で計算する関数
   */
  function calcAmount(){
    var $sumAmount = $("input[name='sumAmount']")[0]; //合計枚数
    var $items = $('.form-block');
    var num_item = $items.length;
    //渉外所持の枚数を計算する
    var pr_have = $sumAmount.value;
    for(var i=1; i<num_item; i++){  //i=0は渉外所持なので飛ばす
      var $item = $("input[name='ticketTypeAmount["+i+"]'");
      var val = $item[0].value;
      if($.isNumeric(val)){
        pr_have -= val;
      }
    }
    //渉外所持の枚数を更新
    $("input[name='ticketTypeAmount[0]'").val(pr_have);
  }
});