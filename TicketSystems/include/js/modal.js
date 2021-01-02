//TODO fadeIn, fadeOutは重そうなので、CSSのtranslateXとかで変更できないか検討する

$(function(){
  $('.js-modal-open').on('click',function(){
    $('.js-modal').fadeIn();
    return false;
  });

  $('.js-modal-close').on('click',function(){
    $('.js-modal').fadeOut();
    return false;
  });
});