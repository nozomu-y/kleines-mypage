$(function(){
  calcAmount(); //ロード時に呼び出す

  //amountのフォームが変更されるたびに呼び出す
  $("input[class*='js-valid-amount']").blur(function(event){
    calcAmount();
  });

  $(".js-form-confirm").on('click',function(event){
    calcAmount();
  });

  /**
   * 渉外所持の枚数(ticketTypeAmount[0])を自動で計算する関数
   */
  function calcAmount(){
    let $sumAmount = $("input[name='sumAmount']")[0]; //合計枚数
    let $items = $('.form-block');
    let num_item = $items.length;
    //渉外所持の枚数を計算する
    let pr_have = $sumAmount.value;
    for(let i=1; i<num_item; i++){  //i=0は渉外所持なので飛ばす
      let $item = $("input[name='ticketTypeAmount["+i+"]'");
      let val = $($item[0]).val();
      if($.isNumeric(val)){
        pr_have -= val;
      }
    }
    //渉外所持の枚数を更新
    $("input[name='ticketTypeAmount[0]'").val(pr_have);
  }
});