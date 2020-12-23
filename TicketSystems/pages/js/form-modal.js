/**
 * フォームの入力確認ボタンが押された時の処理をまとめたjsファイル
 * ・必須項目が入力されているかを確認し、入力されていない場合、色と文字を表示
 * ・入力項目のフォーマット(数値、文字、カナなど)を確認し、適切でない場合色と文字を表示
 * ・適切に入力されていた場合、対応する部分をモーダルに表示する
 * 
 */

 $(function(){
  var forms = document.getElementsByClassName('needs-validation');  //forms:HTMLコレクション。配列と似ている
  var form = forms[0];

  //入力確認ボタンをクリックした時の挙動
  $(".js-form-confirm").on('click', function(){
    var items = form.getElementsByClassName('js-form-item');
    var valid = true;
    //必須項目の入力確認
    for(var i=0; i<items.length; i++){
      //必須項目が未入力の場合、対応するフィードバックを表示
      if(items[i].checkValidity() === false){
        $(items[i]).nextAll('.required-feedback').css({'display':'block'});
        valid = false;
        continue;
      }
      $(items[i]).nextAll('.required-feedback').css({'display':'none'});
      //入力項目のフォーマット確認
      if(items[i].name == "name"){
        //数字が入っていないか
      }else if(items[i].name == "amount"){
        //数値のみか
        //TODO 正規表現に変更
        //TODO 枚数オーバーの確認
        var num = Number(items[i].value);
        console.log(num);
        if(!Number.isInteger(num) || num < 0){
          valid = false;
          $(items[i]).nextAll('.format-feedback').css({'display':'block'});
        }else{
          items[i].value = Number.parseInt(num);
          $(items[i]).nextAll('.format-feedback').css({'display':'none'});
        }
      } //TODO その他、条件ごとに整理する。メソッド化すると良い
    }
    if(valid){
      //モーダルの中身を取得
      var modal_main = document.getElementsByClassName('modal-main');
      var modal_value = modal_main[0].getElementsByClassName('js-form-value');
      //モーダルの中身を置換する
      for(var i=0; i<modal_value.length; i++){
        for(var j=0; j<items.length; j++){
          if(modal_value[i].classList.contains(items[j].name)){
            var value = $(modal_value[i]).children('span');
            var str = value[0].textContent;
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

  $('.js-modal-close').on('click',function(){
    $('.js-modal').fadeOut();
    return false;
  });
});