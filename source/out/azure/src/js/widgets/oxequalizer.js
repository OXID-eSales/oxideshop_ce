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
( function ( $, window ) {

    /**
     * Equalize columns
     */
    var oxEqualizer = {

        /**
         * Gets tallest element value
         *
         * @param {jQuery} elements - The elements to math the height of
         * @returns {Number} - The tallest height in px
         */
        getTallest: function(elements)
        {
            var aHeight = elements.map(function() {
                return $(this).outerHeight();
            }).get();

            return Math.max.apply(null, aHeight);
        },

        /**
         * Match all elements height to the tallest one.
         *
         * @param {jQuery} elements - The elements to math the height of
         * @param {jQuery} target   - Target, which is also included into height calculation
         */
        equalHeight: function(elements, target)
        {
            elements.filter(".oxEqualized").css("height","");
            var tallest = this.getTallest(elements);
            if (target && target.outerHeight() > tallest) {
                tallest = target.outerHeight();
            }
            $.each(elements, function(i, element) {
                $(element).height(tallest - ($(element).outerHeight() - $(element).height())).addClass('oxEqualized');
            });
        }
    };
    
    window.oxEqualizer = oxEqualizer;

})( jQuery, window );
