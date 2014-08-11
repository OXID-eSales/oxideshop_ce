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

    oxModuleConfiguration = {

        _create: function()
        {
            var self = this,
                form      = self.element;

            $('.password_input', form).each(function(i, password) { self.handlePassword(password)});
            form.submit(self.handleSubmit);
        },

        handlePassword : function(password)
        {
            password = $(password);
            var confirmPassword = password.clone().attr('name', '');

            password.before(confirmPassword).before($('</br>'));

            if (!password.data('empty')) {
                confirmPassword.attr('value', '*****');
                password.hide().attr('disabled', true);

                confirmPassword.bind("change paste keyup", function() {
                    console.log();
                    if (!password.is(":visible")) {
                        password.show();
                        password.attr('disabled', false);
                    }
                })
            }

            password.add(confirmPassword).change(function() {
                if (password.attr('value') != '' || confirmPassword.attr('value') == '') {
                    if (password.attr('value') != confirmPassword.attr('value')) {
                        if (password.errorBox == undefined) {
                            password.errorBox = $('<div class="errorbox"></div>').text(password.data('errorMessage'));
                            password.after(password.errorBox);
                        } else {
                            password.errorBox.show();
                        }
                    } else {
                        if (password.errorBox != undefined) {
                            password.errorBox.hide();
                        }
                    }
                }
            });
        },

        handleSubmit : function(event)
        {
            var errors = $('.errorbox');
            if (errors.length > 0) {
                var invalid = false;
                errors.each(function(i, errorBox) {
                    errorBox = $(errorBox);
                    if (errorBox.css('display') != 'none') {
                        $('div:first-child', $(errorBox).parents('div.groupExp')).addClass('exp');
                        invalid = true;
                    }
                });

                if (invalid) {
                    event.stopPropagation();
                    return false;
                }
            }
        }


    };

    $.widget( "ui.oxModuleConfiguration", oxModuleConfiguration );

} )( jQuery );