/**
 * 学年・パートが選ばれたら、名前を絞る
 * 
 * 同伴者？が選ばれたら、form-blockの表示状況を変更する
 */
$(function(){
  //form-block中のselectで、学年・パートが選ばれたら、名前を絞る
  $('.form-block .js-select-grade, .form-block .js-select-part').change(function(event){
    var grade = $(this).parents('.form-block').find('.js-select-grade option:selected').val();  //選ばれた学年
    var part = $(this).parents('.form-block').find('.js-select-part option:selected').val(); //選ばれたパート
    $(this).siblings('.js-select-id')
      .find('option')
      .each(function(index, element){
        //idの中で、js-form-Xの形で選択された条件と比較し、合致するもののみ表示
        var reg_part = new RegExp("js-part-"+part);
        var reg_grade = new RegExp("js-grade-"+grade);
        if(element.className.match(reg_part) != null
        && element.className.match(reg_grade) != null){
          $(element).show();
        }else{
          $(element).hide();
        }
    });
  });
    

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