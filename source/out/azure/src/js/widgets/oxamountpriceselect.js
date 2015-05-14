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
( function( $ ) {
    /**
     * Details amount price selector
     */
    oxAmountPriceSelect = {

        _create: function()
        {
            var self = this,
                options = self.options,
                el      = self.element;

            this.arrow             = this.element;
            this.arrowIcon         = this.arrow.children( 'img' );
            this.priceList         = this.arrow.next( 'ul' );

            this.priceList.css({
                "top": el.position().top - 7,
                "left": el.position().left - 10,
                "width": 220
            });

            // clicking on drop down header
            this.arrow.click(function() {
                self.togglePriceList();
                return false;
            });

            // clicking else where
            $( document ).click( function(){
                self.hideAll();
            });
        },

        /**
         * Shows price list box
         *
         * @return null
         */
        showPriceList : function()
        {
            var arrowSrc = this.arrowIcon.attr("src");
            this.arrowIcon.attr("src", arrowSrc.replace('selectbutton.png', 'selectbutton-on.png'));
            this.priceList.show();
        },

        /**
         * Hides price list box
         *
         * @return null
         */
        hidePriceList : function()
        {
            var arrowSrc = this.arrowIcon.attr("src");
            this.priceList.hide();
            this.arrowIcon.attr("src", arrowSrc.replace('selectbutton-on.png', 'selectbutton.png'));
        },

        /**
         * Hides all lists box
         *
         * @return null
         */
        hideAll : function()
        {
            $('a.js-amountPriceSelector').next( 'ul' ).hide();
            $('a.js-amountPriceSelector').removeClass('js-selected');
        },

        /**
         * toggle list box
         *
         * @return null
         */
        togglePriceList : function ()
        {

            if (!this.isOpened()) {
                this.hideAll();
                this.showPriceList();
                this.arrow.addClass('js-selected');
            } else {
                this.hidePriceList();
                this.arrow.removeClass('js-selected');
            }
        },

        /**
         * returns state of list box
         *
         * @return boolean
         */
        isOpened : function ()
        {
            return this.arrow.hasClass('js-selected');
        }
    }

    $.widget( "ui.oxAmountPriceSelect", oxAmountPriceSelect );

} )( jQuery );