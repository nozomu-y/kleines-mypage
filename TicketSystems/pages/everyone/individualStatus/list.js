$(function(){
  $('td.details .js-modal-open').on('click', function(){
    let params = [];  //削除に必要なパラメータを入れていく連想配列
    $('.modal-main .js-item-list').empty(); //一旦空にする
    $(this).parents('tr.td')
      .find('.js-modal-item')
      .each(function(index, element){
        let className = $(element).attr("class").replace(" js-modal-item","");
        let str = "<p class='tx'>"+className+" : "+element.textContent+"</p>";
      $('.modal-main .js-item-list').append(str);
      if (className === "orderID"){
        params["orderID"] = element.textContent;
      }else if(className === "orderTypeID"){
        params["orderTypeID"] = element.textContent;
      }
    });
    console.log(params);
    //パラメータを書き換え
    $('.modal-footer form input').each(function(index, element){
      console.log($(element).val());
      $(element).val(params[element.name]);
      console.log($(element).val());
    });
    //モーダルに出力
    let modal = '#' + $(this).attr('data-target');
      $(modal).fadeIn();
      return false;
  });

  $('.js-modal-close').on('click', function(){
    $('.modal').fadeOut();
    return false;
  })
});