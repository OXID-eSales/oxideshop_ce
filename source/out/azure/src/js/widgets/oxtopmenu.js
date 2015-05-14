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

    oxTopMenu = {

        _create: function(){

            var self = this,
                options = self.options,
                el      = self.element;


            if ($.browser.msie) {
                $("li:not(:has(ul))", el).hover(function(){
                    $(this).addClass("sfHover");
                }, function(){
                    $(" li:not(:has(ul))", el).removeClass("sfHover");
                });
            }

            //Categories menu init
            el.supersubs({
                minWidth:    12,   // minimum width of sub-menus in em units
                maxWidth:    35,   // maximum width of sub-menus in em units
                extraWidth:  1     // extra width can ensure lines don't sometimes turn over
                                   // due to slight rounding differences and font-family
            }).superfish( {
                 delay : 500,
                 dropShadows : false,
                 onBeforeShow : function() {
                    //adding hover class for active <A> elements
                    $('a:first', this.parent()).addClass($.fn.superfish.op.hoverClass);

                    // horizontaly centering top navigation first level popup accoring its parent
                    activeItem = this.parent()
                    if ( activeItem.parent().hasClass('sf-menu') ) {
                        liWidth = activeItem.width();
                        ulWidth = $('ul:first', activeItem).width();
                        marginWidth = (liWidth - ulWidth) / 2;

                        var itemleft = activeItem.position().left + marginWidth;
                        if (itemleft < 0) marginWidth -= itemleft;

                        var pagewidth = $("#page").outerWidth(),
                        itemright = activeItem.position().left + this.outerWidth() + marginWidth;
                        if (itemright > pagewidth) marginWidth += pagewidth - itemright;

                        $('ul:first', activeItem).css("margin-left", marginWidth);
                    }
                },
                onHide : function() {
                    $('a:first-child',this.parent()).removeClass($.fn.superfish.op.hoverClass);
                }
            });
        }
    }

    $.widget( "ui.oxTopMenu", oxTopMenu );

} )( jQuery );