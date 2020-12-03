module('Compare list');

var sCompareListElement =
'<table id="compare" cellspacing="0" cellpadding="0" border="0" >'+
'<tr>'+
  '<td valign="top">'+
   ' <div id="compareFirstCol" style="overflow: hidden;">'+
      '<table width="200px" cellspacing="0" cellpadding="0" border="1" >'+
        '<tr><td class="tableFirstCol">First Col row1 </td></tr>'+
        '<tr><td class="tableFirstCol">First Col row2</td></tr>'+
        '<tr><td class="tableFirstCol">First Col row3</td></tr>'+
        '<tr><td class="tableFirstCol">First Col row4</td></tr>'+
        '<tr><td class="tableFirstCol">First Col row5 <br /> aaa</td></tr>'+
        '<tr><td class="tableFirstCol">First Col row6</td></tr>'+
      '</table>'+
    '</div>'+
  '</td>'+
  '<td valign="top">'+
    '<div id="compareDataDiv"  style="overflow-x: scroll; width:300px; position:relative">'+
      '<table width="500px" cellspacing="0" cellpadding="0" border="1" >'+
        '<tr id="firstTr">'+
          '<td>Row1Col1</td>'+
          '<td>Row1Col2</td>'+
          '<td>Row1Col3</td>'+
          '<td>Row1Col4</td>'+
          '<td>Row1Col5</td>'+
       '</tr>'+
        '<tr>'+
          '<td>Row2Col1</td>'+
          '<td>Row2Col2</td>'+
          '<td>Row2Col3</td>'+
          '<td>Row2Col4</td>'+
          '<td>Row3Col5</td>'+
        '</tr>'+
        '<tr>'+
          '<td>Row3Col1</td>'+
          '<td>Row3Col2</td>'+
          '<td>Row3Col3</td>'+
          '<td>Row3Col4</td>'+
          '<td>Row3Col5</td>'+
        '</tr>'+
        '<tr>'+
          '<td>Row4Col1</td>'+
          '<td>Row4Col2</td>'+
          '<td>Row4Col3</td>'+
          '<td>Row4Col4</td>'+
          '<td>Row4Col5</td>'+
        '</tr>'+
        '<tr>'+
          '<td>Row5Col1</td>'+
          '<td>Row5Col2</td>'+
          '<td>Row5Col3</td>'+
          '<td>Row5Col4</td>'+
          '<td>Row5Col5</td>'+
        '</tr>'+
        '<tr>'+
          '<td>Row6Col1</td>'+
          '<td>Row6Col2</td>'+
          '<td>Row6Col3 is both wider and<br />taller than surrounding cells, yet<br />fixed elements still line up correctly</td>'+
          '<td>Row6Col4</td>'+
          '<td>Row6Col5</td>'+
        '</tr>'+
      '</table>'+
    '</div>'+
  '</td>'+
'</tr>'+
'</table>';

test('getColumnHeight()', function() {

    var oBody = $('#fixture');
    var oElement = $( sCompareListElement );
    var oColumn;

    oBody.html(oElement);

    oColumn = oxCompare.getOtherColumn(5, 3);

    var rH = oColumn.outerHeight();
    equals( oxCompare.getColumnHeight('mozilla', oColumn) , rH, "height 22");

    oColumn = oxCompare.getOtherColumn(5, 5);
    rH = oColumn.outerHeight();
    equals( oxCompare.getColumnHeight('mozilla', oColumn) , rH, "height 22");

    oBody.html("");

});

test('setColumnHeight()', function() {

    var oBody = $('#fixture');
    var oElement = $( sCompareListElement );

    oBody.html(oElement);

     var oColumn = oxCompare.getOtherColumn(5, 3);

     oColumn = oxCompare.setColumnHeight(oColumn, 150);
     equals( jQuery.trim(oColumn.attr('style')) , 'height: 150px;', "height - 150");

     oColumn = oxCompare.setColumnHeight(oColumn, 250);
     equals( jQuery.trim(oColumn.attr('style')) , 'height: 250px;', "height - 250");

     oColumn = oxCompare.setColumnHeight(oColumn, 0);
     equals( jQuery.trim(oColumn.attr('style')) , 'height: 0px;', "height - 0");

     oBody.html("");
});

test('getOtherColumn()', function() {

    var oBody = $('#fixture');
    var oElement = $( sCompareListElement );

    oBody.html(oElement);

    equals(oxCompare.getOtherColumn(5, 3).is('td'), true, "is column");
    equals(oxCompare.getOtherColumn(5, 2).is('td'), true, "is column");
    equals(oxCompare.getOtherColumn(5, 7).is('td'), false, "not column");
    equals(oxCompare.getOtherColumn(5, 6).is('td'), false, "not column");

    oBody.html("");

});

test('getColumnCount()', function() {

    var oBody = $('#fixture');
    var oElement = $( sCompareListElement );

    oBody.html(oElement);

    equals(oxCompare.getColumnCount(oBody), 5, "5 data columns");

    oBody.html("");

});


