/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   out
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: oxflyoutbox.js 35529 2011-05-23 07:31:20Z vilma $
 */
( function( $ ) {

    oxFlyOutBox = {

        _create: function(){

            var self = this,
                options = self.options,
                el      = self.element;



            $(document).click( function( e ){
                if( $(e.target).parents("div").hasClass("topPopList") ){
                }else{
                    $("div.flyoutBox").hide();
                }
            });

            $(document).keydown( function( e ) {
               if( e.which == 27) {
                    $("div.flyoutBox").hide();
               }
            });

            el.click(function(){
                $("div.flyoutBox").hide();
                $(this).nextAll("div.flyoutBox").show();
                return false;
            });

        }
    }

    $.widget( "ui.oxFlyOutBox", oxFlyOutBox );

} )( jQuery );