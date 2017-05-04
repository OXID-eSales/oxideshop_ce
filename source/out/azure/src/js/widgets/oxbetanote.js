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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */
( function ( $ ) {

    /**
     * Beta note handler
     */
    oxBetaNote = {
        options: {
            cookieName  : "hideBetaNote",
            closeButton : ".dismiss"
        },

        /**
         * Enable beta note dismiss and set cookie to keep it hidden on next pages
         *
         * @return integer
         */
        _create: function() {
            
            var self = this;
            $(self.options.closeButton, self.element).click(
                function(){
                    self.element.fadeOut('slow').remove();
                    $.cookie(self.options.cookieName,1,{path: '/'});
                    
                    if(  $('#cookieNote:visible') ) {
                        $('#cookieNote').animate({ "top": "-=40px" }, 500);
                    }
                    
                    return false;
                }
            );
            
            if( !$.cookie("hideBetaNote") ) {
                $('#betaNote').show();
            } 
            
            if(  $('#cookieNote:visible') ) {
                $('#cookieNote').css('top', '40px');
            }
            
        }
    };

    /**
     * BetaNote widget
     */
    $.widget("ui.oxBetaNote", oxBetaNote );

})( jQuery );
