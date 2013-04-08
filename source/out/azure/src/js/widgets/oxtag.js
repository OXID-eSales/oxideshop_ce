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
 * @version   SVN: $Id: oxtag.js 35529 2011-05-23 07:31:20Z vilma $
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
                    'additionalData' : {'highTags' : oSelf.prev().text()},
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
                        if ( response ) {
                            $(".tagCloud").append("<span class='taggedText'>" + params["newTags"] + "</span> ");
                        } else {
                            $("p.tagError").show();
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