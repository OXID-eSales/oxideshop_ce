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
( function( $ ) {

    oxLoginBox = {

        _create: function(){

            var self = this,
                options = self.options,
                el      = self.element;

            el.click(function(){
                $("#loginBox").show();
                return false;
            });

            $(".altLoginBox .fb_button").on("click", function(){
                $("#loginBox").hide();
            });

            $(document).click( function( e ){
                if( ! $(e.target).parents("div").hasClass("loginBox") ){
                    $("#loginBox").hide();
                }
            });

            $(document).keydown( function( e ) {
               if( e.which == 27) {
                    $("#loginBox").hide();
               }
            });
        }
    }

    $.widget( "ui.oxLoginBox", oxLoginBox );

} )( jQuery );