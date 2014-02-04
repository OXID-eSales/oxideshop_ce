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

    oxModulesList = {

        _create: function() {

            var self = this,
                options = self.options,
                el      = self.element;

            $(".sortable,.sortable2").sortable({
                 opacity: 0.5,
                 update: function() {
                     $("#myedit [name=saveButton]").attr("disabled", "");
                 }
            });

            $("#myedit [name=saveButton]").click(function() {
                var aClasses = $(".sortable").sortable('toArray');

                // make array from current order
                var aModules = {};

                $.each(aClasses, function(key, elem) {
                    sIndex = "#" + elem + "_modules";
                    aModules[elem] = $(sIndex).sortable('toArray');
                });

                $("#myedit [name=aModules]").val(JSON.stringify(aModules));
                $("#myedit").submit();
            })
      }
  }

    $.widget( "ui.oxModulesList", oxModulesList );

} )( jQuery );