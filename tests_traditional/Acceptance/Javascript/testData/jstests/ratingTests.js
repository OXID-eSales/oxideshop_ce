module('Rating');

test('setRatingValue()', function() {

    var oElement = $( '<input id="productRating" type="hidden" name="artrating" value="0">' );

    oElement = oxRating.setRatingValue(oElement, 3);

    equals( oElement.val(), 3, "set value 3");

});

test('setCurrentRating()', function() {

    var oElement = $('<li id="reviewCurrentRating" class="currentRate"><a title=""></a></li>' );

    oElement = oxRating.setCurrentRating(oElement, '40%');

    equals( oElement.css( "width" ), '40%', "set width 40%");

});


test('hideReviewButton()', function() {

    var oElement = $( '<a id="writeNewReview" rel="nofollow"><b>Write a review.</b></a>' );

    oElement = oxRating.hideReviewButton(oElement);

    equals(oElement.css( "display" ) == 'none', true, "hide write review button");


});

test('openReviewForm()', function() {

    var sReviewForm =
        '<div id="review">' +
            '<form action="http://testshops/oxideshop_features/source/index.php?lang=1&amp;" method="post" id="rating">' +
            '<div id="writeReview">' +
            '</div> ' +
            '</form>' +
        '</div>';

    var oBody = $('#fixture');
    var oElement = $( sReviewForm );

    oBody.html(oElement);

    oElement = oxRating.openReviewForm( oElement );

    equals(oElement.css( "display" ) == "block", true, "show form");

    oBody.html("");
});


