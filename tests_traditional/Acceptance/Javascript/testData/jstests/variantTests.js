module('Variants');

test('getLastVariantSelect()', function() {

    var oBody = $('#fixture');
	var oElement = $( '<div><select id="id1" class="blockClass"><option value="1"></option><option value="2"></option></select><select class="blockClass" id="id2"><option value="1"></option><option value="2"></option></select></div>' );

    oBody.html(oElement);

	oElement = oxLoadArticleVariant.getLastVariantSelect(oElement, 'blockClass');
	equals( oElement.attr('id'), 'id2', "get last select with id =  id2 ");
    
    oBody.html("");
});

test('getFirstValue()', function() {

	var oElement = $('<select><option value="1"></option><option value="2"></option></select>' );
	equals( oxLoadArticleVariant.getFirstValue(oElement), 1, "get value 1");

});