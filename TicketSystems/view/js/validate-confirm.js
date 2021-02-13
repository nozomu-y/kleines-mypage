//フォーム取得してモーダルに表示する
/***
 * 入力確認ボタンを押した時に、フォームバリデーションチェック
 * 
 */

 // 無効なフィールドがある場合にフォーム送信を無効にするJavaScript
 window.addEventListener('DOMContentLoaded',function(){
  'use strict';
  $('.needs-validation #btn-confirm').on('click',function(event){
    console.log("this time");
    /*$('form .needs-validation').validate({
      errorElement: "span",
      errorClass: "invalid-message",
      rules: {
        email: {
          email: true,
          //minlength,maxlength,date(日付かどうか),number(整数か)
        },
        amount: {
          number: true,
          min: 1
        },
        price: {
          number: true,
          min: 0,
          step: [500]
        }
      },
      messages: {
        email: {
          email: "メールの形式が正しくありません"
        },
        amount: {
          number: "枚数は整数で入力してください",
          min: "枚数は1以上で入力してください"
        },
        price: {
          number: "値段は整数で入力してください",
          min: "負の数は入力しないでください",
          step: "値段は500の倍数で入力してください"
        }
      },
      errorPlacement : function (err, element){
        element.before(err);
      }
        //lname,fname,lnameKana,fnameKana,
    });*/
    

    var forms = document.getElementsByClassName('needs-validation');  //formsは配列(HTMLコレクション)となる
    var validation = Array.prototype.filter.call(forms,function(form){
      if(form.checkValidity()===false){
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

  
},false);