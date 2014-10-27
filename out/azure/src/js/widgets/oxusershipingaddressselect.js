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
    /**
     * User shipping address selector
     */
    oxUserShipingAddressSelect = {
        _create: function()
        {
            var self = this,
                options = self.options,
                el = self.element;

            el.change(function() {
                var selectValue = $(this).val();

                if ($("input[name=reloadaddress]")) {
                    $("input[name=reloadaddress]").val(self.getReloadValue(selectValue));
                }
                if (selectValue !== '-1') {
                    $( ".js-oxValidate" ).unbind('submit');
                    self.submitForm();
                } else {
                    self.emptyInputFields();
                }
            });
        },

        /**
         * Clears all shipping address input fields
         *
         * @return null
         */
        emptyInputFields : function()
        {
            $("input:text").filter(function() {
                return this.name.match(/address__/);
            }).val("");
            $('#shippingAddressForm').show();
            $('#shippingAddressText').hide();
            $("select[name='deladr[oxaddress__oxcountryid]']").children("option").prop("selected", null);
            $("select[name='deladr[oxaddress__oxstateid]']").children('option[value=""]').prop("selected", "selected");
        },

        /**
         * Sets some form values and submits it
         *
         * @return null
         */
        submitForm : function()
        {
            $("form[name='order'] input[name=cl]").val($("input[name=changeClass]").val());
            $("form[name='order'] input[name=fnc]").val("");
            $("form[name='order']").submit();
        },

        /**
         * Returns reloadaddress value
         *
         * @return integer
         */
        getReloadValue : function( selectValue )
        {
            if (selectValue === '-1') {
                return '1';
            } else {
                return '2';
            }
        }
    }

    $.widget( "ui.oxUserShipingAddressSelect", oxUserShipingAddressSelect );

} )( jQuery );