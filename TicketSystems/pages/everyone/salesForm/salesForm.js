/**
 * 学年・パートが選ばれたら、名前を絞る
 * 
 * 同伴者？が選ばれたら、form-blockの表示状況を変更する
 */
$(function(){
  //チケット預かりを利用しますか？が変更されたら、form-blockの表示状況を変更する
  if($('#reserve').prop('checked')){
    $('.form-block input, .form-block select').prop('disabled', false);
    $('.form-block').show('fast');
  }else{
    $('.form-block input, .form-block select').prop('disabled', true);
  }

  $("#reserve").on('click',function(){
    if ( $(this).prop('checked')) {
      $('.form-block input, .form-block select').prop('disabled', false);
      $('.form-block').show('fast');
    } else {
      $('.form-block input, .form-block select').prop('disabled', true);
      $('.form-block').hide('fast');
    }
  });
});