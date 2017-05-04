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
( function ( $ ) {

    oxTag = {

         highTag : function() {

            var oSelf = $(this);

            $("p.tagError").hide();

            oxAjax.ajax(
                $("#tagsForm"),
                {//targetEl, onSuccess, onError, additionalData
                    'targetEl' : $("#tags"),
                    'additionalData' : {'highTags' : oSelf.prev().text(), 'blAjax' : '1'},
                    'onSuccess' : function(response, params) {
                        oSelf.prev().addClass('taggedText');
                        oSelf.hide();
                    }
                }
            );
            return false;
        },

        saveTag : function() {
            $("p.tagError").hide();

            oxAjax.ajax(
                $("#tagsForm"),
                {//targetEl, onSuccess, onError, additionalData
                    'targetEl' : $("#tags"),
                    'additionalData' : {'blAjax' : '1'},
                    'onSuccess' : function(response, params) {
                        response = JSON.parse(response);
                        if ( response.tags.length > 0 ) {
                            $(".tagCloud").append("<span class='taggedText'>, " + response.tags + "</span> ");
                        }
                        if ( response.invalid.length > 0 ) {
                            var tagError = $("p.tagError.invalid").show();
                            $("span", tagError).text( response.invalid );
                        }
                        if ( response.inlist.length > 0 ) {
                            var tagError = $("p.tagError.inlist").show();
                            $("span", tagError).text( response.inlist );
                        }
                    }
                }
            );
            return false;
        },

        cancelTag : function () {
            oxAjax.ajax(
                $("#tagsForm"),
                {//targetEl, onSuccess, onError, additionalData
                    'targetEl' : $("#tags"),
                    'additionalData' : {'blAjax' : '1', 'fnc' : 'cancelTags'},
                    'onSuccess' : function(response, params) {
                        if ( response ) {
                            $('#tags').html(response);
                            $("#tags #editTag").click(oxTag.editTag);
                        }
                    }
                }
            );
            return false;
        },

        editTag : function() {

            oxAjax.ajax(
                $("#tagsForm"),
                { //targetEl, onSuccess, onError, additionalData
                    'targetEl' : $("#tags"),
                    'additionalData' : {'blAjax' : '1'},
                    'onSuccess' : function(response, params) {

                        if ( response ) {
                            $('#tags').html(response);
                            $("#tags .tagText").click(oxTag.highTag);
                            $('#tags #saveTag').click(oxTag.saveTag);
                            $('#tags #cancelTag').click(oxTag.cancelTag);
                        }
                    }
                }
            );

            return false;
        }
    }

    $.widget("ui.oxTag", oxTag );

})( jQuery );