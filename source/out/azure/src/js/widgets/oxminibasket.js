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
 * @version   SVN: $Id: oxminibasket.js 35529 2011-05-23 07:31:20Z vilma $
 */
( function( $ ) {

    oxMiniBasket = {

        _create: function(){

            var self = this,
                options = self.options,
                el      = self.element;

            var timeout;

            // show on hover after some time
            $("#minibasketIcon", el).hover(function(){
                timeout = setTimeout(function(){
                    self.showMiniBasket();
                }, 2000);
            }, function(){
                clearTimeout(timeout);
            });

            // show on click
            $("#minibasketIcon", el).click(function(){
                self.showMiniBasket();
            });

            // close basket
            $(".closePop").click(function(){
                $(".basketFlyout").hide();
                clearTimeout(timeout);
                return false;
            });

            // show / hide added article message
            if($("#newItemMsg").length > 0){
                $("#countValue").hide();
                $("#newItemMsg").delay(3000).fadeTo("fast", 0, function(){
                    $("#countValue").fadeTo("fast", 1);
                    $("#newItemMsg").remove()
                });
            }

            $("#countdown").countdown(
                function(count, element, container) {
                    if (count <= 1) {
                        //closing and emptying the basket
                        $(element).parents("#basketFlyout").hide();
                        $("#countValue").parent('span').remove();
                        $("#basketFlyout").remove();
                        $("#miniBasket #minibasketIcon").unbind('mouseenter mouseleave');
                        return container.not(element);
                    }
                    return null;
                }
            );

        },

        showMiniBasket : function(){
            $("#basketFlyout").show();

            if ($(".scrollable .scrollbarBox").length > 0) {
                $('.scrollable .scrollbarBox').jScrollPane({
                    showArrows: true,
                    verticalArrowPositions: 'split'
                });
            }
        }
    }

    $.widget( "ui.oxMiniBasket", oxMiniBasket );

} )( jQuery );