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

    var oxMiniBasket = {

        _create: function(){

            var self = this,
                options = self.options,
                el      = self.element;

            var timeout;

            $( "form.js-oxWidgetReload-miniBasket" ).submit( formSubmit );

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

            // close basket on ESC key
            $(document).keydown( function( e ) {
                if( e.which == 27) {
                    $(".basketFlyout").hide();
                    clearTimeout(timeout);
                }
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
                        // refresh mini basket widget
                        $( "form.js-oxWidgetReload-miniBasket" ).submit();
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
        },

        /**
         * Reloads block
         *
         * @param activator
         * @param contentTarget
         * @returns {boolean}
         */
        reload: function ( activator, contentTarget ) {
            oxAjax.ajax(
                activator, {//onSuccess
                    'onSuccess': function ( r ) {
                        $( contentTarget ).html( r );
                        if ( typeof WidgetsHandler !== 'undefined' ) {
                            WidgetsHandler.reloadWidget( 'oxwMiniBasket' );
                        } else {
                            oxAjax.evalScripts( contentTarget );
                        }
                    }
                }
            );
            return false;
        }
    }

    /**
     * Handles form submit
     *
     * @returns {*}
     */
    function formSubmit() {
        var target = $( this );
        return oxMiniBasket.reload( $( target ), $( "#minibasket_container" )[0] );
    }

    $.widget( "ui.oxMiniBasket", oxMiniBasket );

} )( jQuery );