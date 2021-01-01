$(function(){
  $('.js-filter-all').on('click', function(event){
    $(this).siblings('input').prop('checked',true);
  });

  $('.js-filter-clear').on('click', function(event){
    $(this).siblings('input').prop('checked',false);
  });

  $('.js-filter-btn input').change(function(){
    console.log("change input of filter");
    /*
      配列.push(値)で挿入
      $.inArray('値',配列)で検索、indexが返ってくる、ない場合は-1

      ・チェックされた学年・パートを取得
      ・forで全てのタプルを見ていく
      ・その学年・パートに属する人をdisplay:inlineに、そうでない人をdisplay:noneにする
    */
   /*
    //チェックされた学年・パートを取得
    var display_parts = [];
    var display_grades = [];
    $('.filter-checkbox');




    $.inArray($('.js-filter-table td.part').val(), part);
    */
  });
});