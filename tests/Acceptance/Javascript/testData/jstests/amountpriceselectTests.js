module('oxAmountPriceSelect', {

    setup : function() {

        var oBody = $('#fixture');

        var oElement = $(
        '<div class="tobasketFunction clear">'+
            '<a class="selector corners FXgradBlueDark js-amountPriceSelector" href="#priceinfo" id="amountPrice1" rel="nofollow"><img src="http://trunk/out/azure/img/selectbutton.png" alt="Select"></a>'+
            '<ul class="pricePopup corners shadow" id="priceinfo1">'+
                '<li><span><h4>Block price</h4></span></li>'+
                '<li><label>from</label><span>pcs</span></li>'+
                '<li><label>3</label><span>27,90 &euro;</span></li>'+
                '<li><label>3</label><span>25,90 &euro;</span></li>'+
                '<li><label>3</label><span>21,90 &euro;</span></li>'+
            '</ul>'+
        '</div>'+
        '<div class="tobasketFunction clear">'+
            '<a class="selector corners FXgradBlueDark js-amountPriceSelector" href="#priceinfo" id="amountPrice2" rel="nofollow"><img src="http://trunk/out/azure/img/selectbutton.png" alt="Select"></a>'+
            '<ul class="pricePopup corners shadow" id="priceinfo2">'+
                '<li><span><h4>Block price</h4></span></li>'+
                '<li><label>from</label><span>pcs</span></li>'+
                '<li><label>5</label><span>27,90 &euro;</span></li>'+
                '<li><label>10</label><span>25,90 &euro;</span></li>'+
                '<li><label>20</label><span>21,90 &euro;</span></li>'+
            '</ul>'+
        '</div>'
        );
        oBody.html( oElement );
    },

    teardown : function() {
        var oBody = $('#fixture');
        oBody.html("");
    }

});

test("main", function() {
    equals(typeof(oxAmountPriceSelect), "object", "Check object.");

    // methods
    equals(oxAmountPriceSelect.hasOwnProperty("showPriceList"), true, "Check existing method.");
    equals(oxAmountPriceSelect.hasOwnProperty("hidePriceList"), true, "Check existing method.");
    equals(oxAmountPriceSelect.hasOwnProperty("togglePriceList"), true, "Check existing method.");
    equals(oxAmountPriceSelect.hasOwnProperty("hideAll"), true, "Check existing method.");
});


test("showHideSelects", function (){

    // setting widget
    $('a.js-amountPriceSelector').oxAmountPriceSelect();

    equals( $('#priceinfo1').css("display"), 'none', "Value list hidden ");
    equals( $('#priceinfo2').css("display"), 'none', "Value list hidden ");

    // clicking
    $('#amountPrice1').click();
    equals( $('#priceinfo1').css("display"), 'block', "Value list show ");
    equals( $('#priceinfo2').css("display"), 'none', "Value list hidden ");

    $('#amountPrice1').click();
    equals( $('#priceinfo1').css("display"), 'none', "Value list hidden ");
    equals( $('#priceinfo2').css("display"), 'none', "Value list hidden ");

    $('#amountPrice1').click();
    equals( $('#priceinfo1').css("display"), 'block', "Value list hidden ");
    equals( $('#priceinfo2').css("display"), 'none', "Value list hidden ");

    $('#amountPrice2').click();
    equals( $('#priceinfo1').css("display"), 'none', "Value list hidden ");
    equals( $('#priceinfo2').css("display"), 'block', "Value list hidden ");

    $('#amountPrice2').click();
    equals( $('#priceinfo1').css("display"), 'none', "Value list hidden ");
    equals( $('#priceinfo2').css("display"), 'none', "Value list hidden ");

});
