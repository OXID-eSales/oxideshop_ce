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
 * @version   SVN: $Id: oxinnerlabel.js 35529 2011-05-23 07:31:20Z vilma $
 */
( function( $ ) {

    oxInnerLabel = {

        options: {
                sDefaultValue  : 'innerLabel',
                sReloadElement : ''
        },

        _create: function(){

            var self = this,
                options = self.options,
                input = self.element,
                label = $("label[for='"+input.attr('id')+"']");

            self._reload( input, label );

            input.focus(function() {
                label.hide();
            });

            input.blur(function() {
                if ( $.trim(input.val()) == ''){
                    label.show();
                }
            });

            if ($.trim(input.val()) != '') {
                label.hide();
            }
            input.delay(500).queue(function(){
                if ($.trim(input.val()) != '') {
                    label.hide();
                }
            });

            $(options.sReloadElement).click(function() {
                setTimeout(function(){ self._reload( self.element, label ); }, 100);
            });
       },
       
       _reload : function( input, label ){
           var pos = input.position();
           label.css( { "left": (pos.left) + "px", "top":(pos.top) + "px" } );
       }
    }

    $.widget( "ui.oxInnerLabel", oxInnerLabel );

} )( jQuery );