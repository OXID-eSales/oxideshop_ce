module('Article Action Links Select');



test('getLinkboxWidth()', function() {

    var oElement = $( '<span id="productTitle">Product title</span>' );
    equals( oxArticleActionLinksSelect.getLinkboxWidth(200, oElement) , 220, "Width 220");

});


