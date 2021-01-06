$(function(){
  console.log("road promoReq");
  $("#date-determined").on('click',function(){
    console.log("click checkbox");
    if($(this).prop('checked') == false){
      //未チェックになったときの動作
      console.log("not checked");
      $('#date-select input').prop('disabled', true);
    }else{
      console.log("checked");
      $('#date-select input').prop('disabled',false);
    }
  });
});