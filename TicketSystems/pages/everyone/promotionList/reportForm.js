/**
 * 学年・パートが選ばれたら、名前を絞る
 * 
 * 同伴者？が選ばれたら、form-blockの表示状況を変更する
 */
$(function(){
  //form-block中のselectで、学年が選ばれたら、名前を絞る

  //同伴者はいますか？が変更されたら、form-blockの表示状況を変更する
  if($('#accompany').prop('checked')){
    $('.form-block input, .form-block select').prop('disabled', false);
    $('.form-block').show('fast');
  }else{
    $('.form-block input, .form-block select').prop('disabled', true);
  }

  $("#accompany").on('click',function(){
    if ( $(this).prop('checked')) {
      $('.form-block input, .form-block select').prop('disabled', false);
      $('.form-block').show('fast');
    } else {
      $('.form-block input, .form-block select').prop('disabled', true);
      $('.form-block').hide('fast');
    }
  });
});