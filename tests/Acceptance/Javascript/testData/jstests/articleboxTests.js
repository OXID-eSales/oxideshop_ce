
module('oxArticleBox', {

    setup : function() {

        var oBody = $('#fixture');

        var oElement = $(
        	'<div id="trimTitles_1">' +
        		'<div class="box">' +
                	'<h3>' +
                		'<a href="http://eshop/en/By-brand/Manufacturer-EN-Aessue/" id="moreSubCat_8" title="Long manufacturer name very long">1Long manufacturer name very long</a>'+
                	'</h3>' +
                	'<div class="content" style="height: 100px;"></div>'+
                '</div>' +
            '</div>' +
            '<div id="trimTitles_2">'+
            	'<div class="box">' +
            		'<h3>' +
            			'<a href="http://eshop/en/By-brand/Manufacturer-EN-Aessue/" id="moreSubCat_8" title="Long manufacturer name very long (10)">2Long manufacturer name very long (1)</a>'+
            		'</h3>' +
            		'<div class="content" style="height: 100px;"></div>'+
            	'</div>' +
            '</div>'
        );
        oBody.html( oElement );
    },

    teardown : function() {
        var oBody = $('#fixture');
        oBody.html("");
    }

});

test('trimTitlesNoNumbersTest', function() 
{
	// test specific vars
	var oTestBox    = $('#trimTitles_1'),		
		iMaxWidth   = $('h3', oTestBox).width(),
		sEndPattern = /…$/;
	
	// running the oxArticleBox widget
	oTestBox.oxArticleBox();		
	
	// checking if the new title's length doesn't exceed `iMaxWidth`
	ok($('h3 a', oTestBox).width() <= iMaxWidth, "Title width is equal or shorter than it's container");

	// checking if the new title ends with '...'
	ok(sEndPattern.test($('h3 a', oTestBox).html()), "Title ends with '…'");	
});

test('trimTitlesTests', function()
{
	// test specific vars
	var oTestBox    = $('#trimTitles_2'),
		iMaxWidth   = $('h3', oTestBox).width(),
		sEndPattern = /… \([0-9]+\)$/;	
	
	// running the oxArticleBox widget
	oTestBox.oxArticleBox();
	
	// checking if the new title's length doesn't exceed `iMaxWidth`
	ok($('h3 a', oTestBox).width() <= iMaxWidth, "Title width is equal or shorter than it's container");
	
	// checking if the new title ends width '... (number)'
	ok(sEndPattern.test($('h3 a', oTestBox).html()), "Title ends with '… (number)'");	
})