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

     oxModalPopup = {
            options: {
                width        : 687,
                height       : 'auto',
                modal        : true,
                resizable    : true,
                zIndex       : 10000,
                position     : 'center',
                draggable    : true,
                target       : '#popup',
                openDialog   : false,
                loadUrl      : false
            },

            _create: function() {

                var self = this,
                options = self.options,
                el      = self.element;

                if (options.openDialog) {

                    if (options.loadUrl){
                        $(options.target).load(options.loadUrl);
                    }

                    self.openDialog(options.target, options);

                } else {

                    el.click(function(){

                        if (options.loadUrl){
                            $(options.target).load(options.loadUrl);
                        }

                        self.openDialog(options.target, options);

                        return false;
                    });
                }

                $("img.closePop, button.closePop", $( options.target ) ).click(function(){
                    $( options.target ).dialog("close");
                    return false;
                });
            },

            openDialog: function (target, options) {
                $(target).dialog({
                    width     : options.width,
                    height    : options.height,
                    modal     : options.modal,
                    resizable : options.resizable,
                    zIndex    : options.zIndex,
                    position  : options.position,
                    draggable : options.draggable,

                    open: function(event, ui) {
                        $('div.ui-dialog-titlebar').remove();
                    }
                });
            }
    };

    $.widget("ui.oxModalPopup", oxModalPopup );

} )( jQuery );