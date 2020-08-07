window.addEventListener('DOMContentLoaded',function(){
  // フォームカウント
  var form_count = $('#init').val();
  var select = [];

  //accompanyが選択されているかどうかの初期処理
  if($('#accompany').prop('checked')==false){
    $('.form-block :input').prop('disabled',true);
  }else{
    $('.form-block :input').prop('disabled',false);
    $('.form-block').show();
  }

  //accompanyがクリックされた時の挙動
  $("#accompany").on('click',function(){
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

  $('span.add-button').on('click',addMember);  //addボタンがクリックされた時の挙動
  $('span.remove-button').on('click',removeMember);  //removeボタンがクリックされた時の挙動

  //同行者の学年・パートが選択された時の挙動
  $('select[id^=member_grade],select[id^=member_part]').change(function(){
    var index = $(this).attr('id').search(/\d+/);
    var id = $(this).attr('id').substr(index,1);
    var grade = $('select[id=member_grade\\['+id+'\\]]').val();
    var part = $('select[id=member_part\\['+id+'\\]]').val();
    if(grade!="" && part!=""){ //そのIDのgrade,partが共に選択されていたら
      addSelectMenu(id,grade,part);
    }
  });

  //ページを読み込んだ時に、学年パートから名前の一覧を作成する
  for(var i=0;i<=form_count;i++){
    var grade = $('select[id=member_grade\\['+i+'\\]]').val();
    var part = $('select[id=member_part\\['+i+'\\]]').val();
    
    if(grade!="" && part!=""){ //そのIDのgrade,partが共に選択されていたら
      console.log("start when");
      //selectedの値を取得する
      select[i] = $('#member_name\\['+i+'\\]').val();
      console.log("selected="+select);
      //addSelectMenu(i,grade,part);
      addSelectMenuWithCallback(i,grade,part,selectElement); 
    }
  }

  function selectElement(i){
    console.log("done:start done");
    //初期値を選択していたものにする
    //全ての選択肢の中で、selectと一致するものがあったらselectedを付与
    $('select[id=member_name\\['+i+'\\]]').find('option').each(function(index,element){
      console.log("done:"+index+":"+$(element).text());
      if($(element).val()==select[i]){
        console.log("done:find selected");
        element.selected = true;
        $(element).prop('selected',true);
      }
    });
    console.log("done:finish done");
  }
  //memo バリデーションチェックをここで行う
  //memo textが空欄なものを検出して返す(他のでも使ってるやつ)
  //memo プルダウンメニューで選んでいるものの値が""のとき跳ね返す

  function addSelectMenu(id,grade,part){
    console.log("start addSelectMenu");
    $.ajax({
      type:"POST",
      url: "../../model/donePromotion_ajax.php",   //サーバ側のphp
      data: {                //呼び出し先のパラメータ
          "grade": grade,
          "part": part
      },
     dataType: 'json'        //サーバ側からの返却形式、XMLとかも可能
    }).done(function(names) {  //成功した場合に行う処理
      //既存の選択肢を全て削除し、検索結果を全て挿入する
      $('#member_name\\['+ id +'\\] option').remove();  //既存の選択肢を全て削除
      $('#member_name\\['+id+'\\]').append('<option value="">未選択</option>'); //未選択を追加
      for(var j=0;j<Object.keys(names).length;j++){  //dataの要素数(SELECTの件数)だけループ
        $('#member_name\\['+id+'\\]').append('<option value="'+names[j]+'">'+names[j]+'</option>');
        console.log("id="+j+":"+names[j]+" appended");
      }
      console.log("finish addSelectMenu");
      //memo 代表者をリストから外す
      //memo すでに入力済みの人をリストから外す
    }).fail(function(XMLHttpRequest, textStatus, errorThrown) {  //失敗した場合に行う処理
      console.log("XMLHttpRequest : " + XMLHttpRequest.status);
      console.log("textStatus     : " + textStatus);
      console.log("errorThrown    : " + errorThrown.message);
      console.log("fail to ajax");
    });
  }
  
  function addSelectMenuWithCallback(id,grade,part,callback){
    console.log("start addSelectMenuWithCallback");
    $.ajax({
      type:"POST",
      url: "../../model/donePromotion_ajax.php",   //サーバ側のphp
      data: {                //呼び出し先のパラメータ
          "grade": grade,
          "part": part
      },
     dataType: 'json'        //サーバ側からの返却形式、XMLとかも可能
    }).done(function(names) {  //成功した場合に行う処理
      //既存の選択肢を全て削除し、検索結果を全て挿入する
      $('#member_name\\['+ id +'\\] option').remove();  //既存の選択肢を全て削除
      $('#member_name\\['+id+'\\]').append('<option value="">未選択</option>'); //未選択を追加
      for(var j=0;j<Object.keys(names).length;j++){  //dataの要素数(SELECTの件数)だけループ
        $('#member_name\\['+id+'\\]').append('<option value="'+names[j]+'">'+names[j]+'</option>');
        console.log("id="+j+":"+names[j]+" appended");
      }
      console.log("finish addSelectMenu");
      callback(id);
      //memo 代表者をリストから外す
      //memo すでに入力済みの人をリストから外す
    }).fail(function(XMLHttpRequest, textStatus, errorThrown) {  //失敗した場合に行う処理
      console.log("XMLHttpRequest : " + XMLHttpRequest.status);
      console.log("textStatus     : " + textStatus);
      console.log("errorThrown    : " + errorThrown.message);
      console.log("fail to ajax");
    });
    
  }
  
  function addMember(){
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
      .find('input,select').each(function(index,obj){
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
  
  function removeMember(){
    $(this).css('color','#7799cc');
    //自分のid番号を取得
    //自分のid番号をもつ親要素のidを取得
    //hideしてremove
    var $removeObj = $(this).parents('.form-block');
    $removeObj.hide('fast');
    $removeObj.remove();
    // 番号振り直し
    form_count = 0;
    $(".form-block[id^='form-block']").each(function(index, formObj) {
      if ($(formObj).attr('id') != 'form-block[0]') {
        form_count++;
        $(formObj)
          .attr('id', 'form-block\[' + form_count + '\]') // id属性を変更
          .find('input, textarea,select').each(function(idx, obj) {
          $(obj).attr({
            id: $(obj).attr('id').replace(/\[[0-9]\]+$/, '[' + form_count + ']'),
            name: $(obj).attr('name').replace(/\[[0-9]\]+$/, '[' + form_count + ']')
          });
        });
      }
    });
  };
});

