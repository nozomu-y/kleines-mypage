$(function(){
  if($('#date-determined').prop('checked')){  //ページ読み込み時
    $('#date-select input').prop('disabled', false);
  }else{
    $('#date-select input').prop('disabled', true);
  }
  
  $("#date-determined").on('click',function(){  //チェックボックス変更時
    if($(this).prop('checked')){
      $('#date-select input').prop('disabled', false);
    }else{
      $('#date-select input').prop('disabled', true);
    }
  });
});