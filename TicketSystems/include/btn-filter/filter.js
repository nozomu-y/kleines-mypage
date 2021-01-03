$(function(){
  var SENIOR_KEYWORD = "以上";  //上級生を表すキーワード

  $('.js-filter-all').on('click', function(event){
    $(this).siblings('input').prop('checked',true);
    filter();
  });

  $('.js-filter-clear').on('click', function(event){
    $(this).siblings('input').prop('checked',false);
    filter();
  });

  $('.js-filter-btn input').change(filter);

  /**
   * テーブルのフィルタリングを行う関数
   */
  function filter(){
    var parts = [];
    var grades = [];
    //条件を取得
    $('.member-filter .js-filter-part .filter-checkbox').each(function(index, element){
      if($(element).prop('checked')){
        parts.push(element.value);
      }
    });
    $('.member-filter .js-filter-grade .filter-checkbox').each(function(index, element){
      if($(element).prop('checked')){
        grades.push(element.value);
      }
    });

    //上級生の境目の学年を取得
    var border = getSeniorGrade(grades);

    //各タプルに対して条件分岐と処理を行う
    $('tr.td').each(function(index, element){
      var filtered_grade = false;
      var filtered_part = false;
      if($.inArray($(element).find('td.grade').text(), grades) < 0){  //学年がgradesに含まれないか
        filtered_grade = true;
      }
      if(filtered_grade && border != null){  //学年がgradesに含まれないが、上級生枠が存在する時
        if(Number($(element).find('td.grade').text()) <= border){  //入学年度がborder以下かを調べる
          filtered_grade = false;
        }
      }
      if($.inArray($(element).find('td.part').text(), parts) < 0){  //パートがpartsに含まれないか
        filtered_part = true;
      }
      //条件が真なら隠す&チェックボックスをdisabledに変更
      if(filtered_grade || filtered_part){
        $(element).hide();
        $(element).find('.flag input').prop("disabled",true);
      }else{
        $(element).show();
        $(element).find('.flag input').prop("disabled",false);
      }
    });
  }

  /**
   * arrayの中に"〇〇以上"という文字列が含まれているとき、〇〇の部分を返す関数
   * "以上"に相当する、上級生を表すキーワードは、SENIOR_KEYWORDを変えてください
   * @param array 文字列が入った配列
   * @return border_grade 〇〇の部分の文字列
   * @return null if not exist
   */
  function getSeniorGrade(array){
    var border_grade = null;
    $(array).each(function(index, element){
      if(element.match(SENIOR_KEYWORD) != null){
        border_grade = element.replace(SENIOR_KEYWORD, '');
      }
    });
    return border_grade;
  }

});