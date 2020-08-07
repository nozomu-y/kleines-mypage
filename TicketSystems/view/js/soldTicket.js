window.addEventListener('DOMContentLoaded',function(){
  // フォームカウント
  var form_count = 0;
  //memo 戻ってきたときはこの変数の値を変えたい
  if($('#reserve_use_true').prop('checked')==false){
    $('.form-block :input').prop('disabled',true);
  }else{
    $('.form-block :input').prop('disabled',false);
    $('.form-block').show();
  }

  $("#reserve_use_true").on('click',function(){
    if ( $(this).prop('checked') == false ) {
      //未チェックになったときの動作
      $('.form-block :input').prop('disabled', true);
      $('.form-block').hide('fast');
    } else {
      $('.form-block :input').prop('disabled',false);
      $('.form-block').show('fast');
    }
  });

  //remove-button,add-buttonにカーソルが乗った時のみ色を変える
  $('.addrem-button span').mouseover(function(){
    $(this).css('color','#77ffff');
  });
  $('.addrem-button span').mouseout(function(){
    $(this).css('color','#666666');
  });

  $('span.add-button').on('click',addReserve);  //addボタンがクリックされた時の挙動
  $('span.remove-button').on('click',removeReserve);  //removeボタンがクリックされた時の挙動

  //読み込み時に、POSTに配列があったらそれの回数分addReserveを呼び出す？
  function addReserve(){
    //form-blockの内容をコピーし、次の部分に表示する
    $(this).css('color','#7799cc');
    var $original = $('#form-block\\[' + form_count + '\\]'); //最後のブロックを選択
    form_count++; //idの更新
    $original
      .clone(true)
      .hide()
      .insertAfter($original)
      .attr('id', 'form-block\[' + form_count + '\]') // クローンのid属性を変更
      .end()
      .find('input').each(function(index,obj){
        $(obj).attr({
          id: $(obj).attr('id').replace(/\[[0-9]\]+$/, '[' + form_count + ']'),  //idの書き換え
          name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + form_count + ']') //nameの書き換え
        });
        $(obj).val('');
      });
    // clone取得
    var clone = $('#form-block\\[' + form_count + '\\]');
    clone.find('span.remove-button').show();
    clone.show('fast');
  };
  
  function removeReserve(){
    $(this).css('color','#7799cc');
    //自分のid番号を取得
    //自分のid番号をもつ親要素のidを取得
    //hideしてremove
    var $removeObj = $(this).parents('.form-block');
    //$removeObj.hide('fast', function() {  //fadeout後にfunctionを呼び出し
    $removeObj.remove();
    // 番号振り直し
    form_count = 0;
    $(".form-block[id^='form-block']").each(function(index, formObj) {
      if ($(formObj).attr('id') != 'form-block[0]') {
        form_count++;
        $(formObj)
          .attr('id', 'form-block\[' + form_count + '\]') // id属性を変更
          .find('input, textarea').each(function(idx, obj) {
          $(obj).attr({
            id: $(obj).attr('id').replace(/\[[0-9]\]+$/, '[' + form_count + ']'),
            name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + form_count + ']')
          });
        });
      }
    });
  };
});

