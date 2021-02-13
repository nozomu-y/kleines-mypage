$(function(){
  sumUp();

  $('.js-filter-all, .js-filter-clear').on('click', function(event){
    sumUp();
  });

  $('.js-filter-btn input').change(sumUp);

  function sumUp(){
    var sumSold = 0;
    var sumHave = 0;
    var count = 0;  //for test
    //table[id=all]で、表示されている人のtrを取得し、td.sold, td.haveの枚数を足し合わせる
    $('table.js-filter-table tr.td').each(function(index,element){
      if($(element).is(':visible')){
        sumSold += Number($(element).find('td.sold').text());
        sumHave += Number($(element).find('td.have').text());
        count += 1;
      }
    });
    //合計を、table#summaryのtd.sold, td.haveに書き込む
    $('table#summary tr.td td').each(function(index,element){
      if(element.className == "sold"){
        $(element).text(sumSold);
      }else if(element.className == "have"){
        $(element).text(sumHave);
      }else if(element.className == "count"){
        $(element).text(count);
      }
    });
  }
});