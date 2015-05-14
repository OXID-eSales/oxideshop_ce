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
     * Cookie note handler
     */
    oxCookieNote = {
        options: {
            closeButton : ".dismiss"
        },
        /**
         * Enable cookie note dismiss
         *
         * @return false
         */
        _create: function() {
            var self = this;

            $.cookie('cookiesEnabledCheck', 'yes');

            if ($.cookie('cookiesEnabledCheck')) {
                $.cookie('cookiesEnabledCheck', null, -1);

                if( !$.cookie("displayedCookiesNotification") ) {
                    $.cookie("displayedCookiesNotification", 1, { path: '/', expires: 30 });
                    $('#cookieNote').show();

                    // need to add this even only if we decide to show cookie note
                    $(self.options.closeButton, self.element).click(
                        function(){
                            self.element.fadeOut('slow').remove();
                            return false;
                        }
                    );
                } else {
                    self.element.remove();
                    return false;
                }
            }
        }
    };

    /**
     * CookieNote widget
     */
    $.widget("ui.oxCookieNote", oxCookieNote );

})( jQuery );