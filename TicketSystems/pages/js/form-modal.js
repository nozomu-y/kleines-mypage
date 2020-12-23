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
  console.log("initial call 0");

  $(".js-form-confirm").on('click', function(){
    console.log("enter button");
    //確認ボタンが押された時の挙動
    //必須項目の入力確認
    var items = form.getElementsByClassName('js-form-item');
    console.log(items);
    var valid = true;
    for(var i=0; i<items.length; i++){
      console.log(items[i].name+":"+items[i].value);
      if(items[i].checkValidity() === false){
        console.log("empty!");
        $(items[i]).nextAll('.required-feedback').css({'display':'block'});
        valid = false;
      }else{
        console.log("full!");
        $(items[i]).nextAll('.required-feedback').css({'display':'none'});
        //入力項目のフォーマット確認
        console.log(items[i].name);
        if(items[i].name == "name"){
          //数字が入っていないか
        }else if(items[i].name == "amount"){
          //数値のみか
          //TODO 枚数オーバーの確認
          if(!Number.isInteger(Number(items[i].value))){
            valid = false;
            console.log("not integer");
            $(items[i]).nextAll('.format-feedback').css({'display':'block'});
          }else{
            console.log("this is integer");
            $(items[i]).nextAll('.format-feedback').css({'display':'none'});
          }
        } //TODO その他、条件ごとに整理する。メソッド化すると良い
      }
    }
    if(valid){
      console.log("valid!");
      //値を取得
      //モーダルの中身を置換
      var modal_main = document.getElementsByClassName('modal-main');
      console.log(modal_main);
      var modal_value = modal_main[0].getElementsByClassName('js-form-value');
      console.log(modal_value);
      for(var i=0; i<modal_value.length; i++){
        for(var j=0; j<items.length; j++){
          if(modal_value[i].classList.contains(items[j].name)){
            var value = $(modal_value[i]).children('span');
            console.log("value "+value[0]);
            var str = value[0].textContent;
            console.log("str "+str);
            value[0].innerHTML = items[j].value;
          }
        }
      }
      //モーダルに出力
      $('.js-modal').fadeIn();
      return false;
    }else{
      console.log("not valid");
    }
    /*
    
    */
    return false;  //動作をストップ
  });

  $('.js-modal-close').on('click',function(){
    $('.js-modal').fadeOut();
    return false;
  });
});