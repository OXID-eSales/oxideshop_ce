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

    oxModuleConfiguration = (function() {

        /**
         * Object for jQuery widget creation.
         * @type {{_create: Function}}
         */
        var obj = {
            _create: function () {
                var form = this.element;

                $('.password_input', form).each(handlePassword);
                form.submit(handleSubmit);
            }
        };

        /**
         * All password fields in the form.
         * @type {Array}
         */
        var passwordFields = [];

        /**
         * Handles password field actions.
         * @param position
         * @param password
         */
        function handlePassword(position, password)
        {
            password = $(password);
            var passwordConfirm = password.clone().attr('name', '');

            passwordFields.push({original: password, confirmation: passwordConfirm});

            password.before(passwordConfirm).before($('</br>'));

            if (!password.data('empty')) {
                hidePassword(password, passwordConfirm);
            }

            password.add(passwordConfirm).change(function () {
                if (password.attr('value') != '' || passwordConfirm.attr('value') == '') {
                    checkPassword(password, passwordConfirm);
                }
            });
        }

        /**
         * Handle form submit action.
         * @param event
         * @returns {boolean}
         */
        function handleSubmit(event)
        {
            var invalid = false;
            $(passwordFields).each(function (position, element) {
                if (!checkPassword(element.original, element.confirmation)) {
                    $('div:first-child', element.original.parents('div.groupExp')).addClass('exp');
                    invalid = true;
                }
            });

            if (invalid) {
                event.stopPropagation();
                return false;
            }
        }

        /**
         * Hides original password, sets value to confirmation password and shows original password on event.
         * @param password
         * @param passwordConfirm
         */
        function hidePassword(password, passwordConfirm)
        {
            passwordConfirm.attr('value', '*****');
            password.hide().attr('disabled', true);

            passwordConfirm.bind("change paste keyup", function () {
                if (!password.is(":visible")) {
                    password.show().attr('disabled', false);
                }
            })
        }

        /**
         * Checks whether passwords matches.
         * @param original
         * @param confirm
         * @returns {boolean}
         */
        function checkPassword(original, confirm)
        {
            var result = true;
            if (original.attr('disabled') == false && original.attr('value') != confirm.attr('value')) {
                if (original.errorBox == undefined) {
                    original.errorBox = $('<div class="errorbox"></div>').text(original.data('errorMessage'));
                    original.after(original.errorBox);
                } else {
                    original.errorBox.show();
                }
                result = false;
            } else if (original.errorBox != undefined) {
                original.errorBox.hide();
            }

            return result;
        }

        return obj;
    })();

    $.widget( "ui.oxModuleConfiguration", oxModuleConfiguration );

} )( jQuery );