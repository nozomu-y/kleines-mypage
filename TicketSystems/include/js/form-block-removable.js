// ブロックを削除・追加できるform-blockの挙動を制御するjsファイル

window.addEventListener('DOMContentLoaded',function(){
  //既に存在するform-blockの最大ID
  var max_form_index = $(".form-block").length - 1;

  $('button.js-fb-add').on('click',addFormBlock);  //addボタンがクリックされた時の挙動
  $('button.js-fb-remove').on('click',removeFormBlock);  //removeボタンがクリックされた時の挙動

  function addFormBlock(){
    //form-blockの内容をコピーし、次の部分に表示する
    var $original = $('#form-block\\[' + max_form_index + '\\]'); //最後のブロックを選択
    max_form_index++; //idの更新
    $original
      .clone(true)
      .hide()
      .insertAfter($original)
      .attr('id', 'form-block\[' + max_form_index + '\]') // クローンのid属性を変更
      .end()
      .find('input').each(function(index,obj){
        $(obj).attr({
          name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + max_form_index + ']') //nameの書き換え
        });
        $(obj).val('');
      });
    var clone = $('#form-block\\[' + max_form_index + '\\]'); // clone取得
    //removableにし、削除ボタンをshow
    $(clone).addClass('js-fb-removable');
    clone.find('.js-fb-remove').show();
    clone.show('fast');
  };
  
  function removeFormBlock(){
    var $removeObj = $(this).parents('.js-fb-removable');
    //$removeObj.hide('fast', function() {  //fadeout後にfunctionを呼び出し
    $removeObj.remove();
    // 番号振り直し
    max_form_index = 0;
    $(".form-block[id^='form-block']").each(function(index, formObj) {
      if ($(formObj).attr('id') != 'form-block[0]') {
        max_form_index++;
        $(formObj)
          .attr('id', 'form-block\[' + max_form_index + '\]') // id属性を変更
          .find('input, textarea').each(function(idx, obj) {
          $(obj).attr({
            name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + max_form_index + ']')
          });
        });
      }
    });
  };
});

