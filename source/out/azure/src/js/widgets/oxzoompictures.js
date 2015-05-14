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

    oxZoomPictures = {

        options: {
            sMorePicsContainerId     : "#morePicsContainer",
            sMoreZoomPicsContainerId : "#moreZoomPicsContainer",
            sZoomImgId               : "#zoomImg",
            sZoomTriggerButtonId     : "#zoomTrigger"
        },

        _create: function() {

            var self    = this,
                options = self.options,
                el      = self.element;

            $("li a", el).click(function() {
                $(options.sZoomImgId).attr("src", $(this).attr("href"));

                return false;
            });

             // adding click event for zoom button
             $(options.sZoomTriggerButtonId).click(function() {
                 self._beforeShow();
             } );

        },

        /*
         * Checking which picture was selected in product details view
         * and zooming this selected picture
         */
        _beforeShow: function() {
            var self    = this,
                options = self.options,
                el      = self.element;

            iIndex = $(options.sMorePicsContainerId + " li a.selected").parent().index();
            $(options.sMoreZoomPicsContainerId).oxMorePictures({iDefaultIndex: iIndex});
        }
    }

    $.widget( "ui.oxZoomPictures", oxZoomPictures );

} )( jQuery );


