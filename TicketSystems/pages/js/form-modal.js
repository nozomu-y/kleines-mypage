/**
 * フォームの入力確認ボタンが押された時の処理をまとめたjsファイル
 * ・必須項目が入力されているかを確認し、入力されていない場合、色と文字を表示
 * ・入力項目のフォーマット(数値、文字、カナなど)を確認し、適切でない場合色と文字を表示
 * ・適切に入力されていた場合、対応する部分をモーダルに表示する
 * 
 */

 $(function(){
  var forms = document.getElementsByClassName('needs-validation');

  //入力確認ボタンをクリックした時の挙動
  $(".js-form-confirm").on('click', function(){
    var num_item = 0;
    var items;
    if(forms.length>0){
      items = forms[0].getElementsByClassName('js-form-item');
      num_item = items.length;
    }
    var valid = true;
    //必須項目の入力確認
    for(var i=0; i<num_item; i++){
      //必須項目が未入力の場合、対応するフィードバックを表示
      if(items[i].checkValidity() === false){
        show_invalid(items[i], '.required-feedback');
        valid = false;
        continue;
      }
      hide_invalid(items[i], '.required-feedback');
      //入力項目のフォーマット確認
      var valid_item = true;  //そのアイテムのバリデーション結果
      //特殊文字のエスケープ
      if(items[i].value.match(/(<|>|&|"|')/)){
        valid = false;
        valid_item = false;
        show_invalid(items[i], '.invalid-chars');
        continue;
      }else{
        hide_invalid(items[i], '.invalid-chars');
      }
      if(items[i].name == "name"){
        if(items[i].value.match(/\d/)){ //数字が入っていないか
          valid = false;
          valid_item = false;
        }
      }else if(items[i].name == "amount"){
        //TODO 枚数オーバーの確認
        var num = Number(items[i].value);
        if(!Number.isInteger(num) || num < 0){  //0以上の整数のみ
          valid = false;
          valid_item = false;
        }else{
          items[i].value = Number.parseInt(num);
        }
      }else if(items[i].name.match("kana")){
        if(!(items[i].value.match(/^[\u30a0-\u30ff]+$|^[\u3040-\u309f]+$/))){  //全角カナorかなのみ
          valid = false;
          valid_item = false;
        }
      }
      //TODO その他、条件ごとに整理する
      if(!valid_item){
        show_invalid(items[i], '.format-feedback');
      }else{
        hide_invalid(items[i], '.format-feedback');
      }
    }
    if(valid){
      //モーダルの中身を取得
      var modal_main = document.getElementsByClassName('modal-main');
      var modal_value = modal_main[0].getElementsByClassName('js-form-value');
      //モーダルの中身を置換する
      for(var i=0; i<modal_value.length; i++){
        for(var j=0; j<num_item; j++){
          if(modal_value[i].classList.contains(items[j].name)){
            var value = $(modal_value[i]).children('span');
            value[0].innerHTML = items[j].value;
          }
        }
      }
      //モーダルに出力
      $('.js-modal').fadeIn();
      return false;
    }
    return false;  //動作をストップ
  });

  //モーダル出力時の閉じる動作
  $('.js-modal-close').on('click',function(){
    $('.js-modal').fadeOut();
    return false;
  });
});

function show_invalid(item, msg_class){
  $(item).css({'box-shadow':'2px 2px #f14343, -2px -2px #f14343'});
  $(item).nextAll(msg_class).css({'display':'block'});
}

function hide_invalid(item, msg_class){
  $(item).css({'box-shadow':'none'});
  $(item).nextAll(msg_class).css({'display':'none'});
}