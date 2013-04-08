
$(document).ready(function(){
    $('div.promotionsRow .promotion .promoTimeout')
        .countdown(
            function(count, element, container) {
                if (count > 1) {
                    if (count<1800) {
                        $(element).css('font-size', 'large');
                    }
                } else {
                    var promo = $(element).parents('div.promotion');
                    if (promo.hasClass('promotionFuture')) {
                        promo.removeClass( 'promotionFuture' );
                        promo.addClass( 'promotionCurrent' );
                        return container.filter(':not(.promotionCurrent .activationtext .promoTimeout)');
                    } else if (promo.hasClass('promotionCurrent')) {
                        promo.removeClass( 'promotionCurrent' );
                        promo.addClass( 'promotionFinished' );
                        return container.filter(':not(.promotionFinished .timeouttext .promoTimeout)');
                    }
                }
                return null;
            }
    );
});
