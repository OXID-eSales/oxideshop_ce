/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
( function ( $ ) {

    /**
     * Equalize columns
     */
    var oxEqualizer = {

        /**
         * Gets tallest element value
         *
         * @param group
         * @param target
         * @returns {Number} - The tallest height
         */
        equalHeight: function(group, target)
        {
            var self     = this,
                newH     = 0,
                tallest  = 0,
                elementH = 0;

            if ( target ) {
                if (group.height() < target.height()){
                    tallest = target.height() - (group.innerHeight() - group.height());
                }
            } else {
                tallest = self.getTallest( group );
            }

            if ( tallest ) {
                group.each(function(){
                    if($(this).hasClass('oxEqualized')) {
                        $(this).css('height','');
                        $(this).removeClass('oxEqualized');
                    }
                    elementH = $(this).outerHeight();
                    if (elementH < tallest) {
                        newH = tallest - (elementH - $(this).height());
                        $(this).height(newH).addClass('oxEqualized');
                    }
                });
            }
        },

        /**
         * Gets tallest element value
         *
         * @returns {Number} - The tallest height
         */
        getTallest: function(el)
        {
            var tallest = 0;
            el.each(function(){
                var thisHeight = $(this).outerHeight();
                if (!$(this).hasClass('oxEqualized') && thisHeight > tallest) {
                    tallest = thisHeight;
                }
            });
            return tallest;
        }
    };

    /**
     * Equalizer widget
     */
    $.widget("ui.oxEqualizer", oxEqualizer );

})( jQuery );
