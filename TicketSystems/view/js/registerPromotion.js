window.addEventListener('DOMContentLoaded',function(){
  if($('#date_determined').prop('checked')==false){
    $('#date_select select').prop('disabled',true);
  }else{
    $('#date_select select').prop('disabled',false);
  }

  $("#date_determined").on('click',function(){
    if ( $(this).prop('checked') == false ) {
      //未チェックになったときの動作
      $('#date_select select').prop('disabled', true);
    } else {
      $('#date_select select').prop('disabled',false);
    }
  });

});