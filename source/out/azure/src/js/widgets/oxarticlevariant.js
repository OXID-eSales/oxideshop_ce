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
 * @version   SVN: $Id: oxarticlevariant.js 35529 2011-05-23 07:31:20Z vilma $
 */
( function ( $ ) {

    var oxArticleVariant = {

        _create: function() {
            $("ul.vardrop a").click( variantSelectActionHandler );
            $('div.variantReset a').click( variantResetActionHandler );

            $("form.js-oxWidgetReload").submit( formSubmit );
            $("form.js-oxProductForm").submit( formSubmit );
        },

        reload: function(activator, highlightTargets, contentTarget, aOptions) {
            this._preAjaxCaller();
            oxAjax.ajax(
                activator, {//targetEl, onSuccess, onError, additionalData
                    'targetEl'  : highlightTargets,
                    'iconPosEl' : $("#variants .dropDown"),
                    'additionalData' : aOptions,
                    'onSuccess' : function(r) {
                        contentTarget.innerHTML = r;
                        if ( typeof WidgetsHandler !== 'undefined') {
                            WidgetsHandler.reloadWidget('oxwarticledetails');
                        } else {
                            oxAjax.evalScripts(contentTarget);
                        }
                    }
                }
            );
            return false;
        },
        resetVariantSelections : function() {
            var aVarSelections = $( "form.js-oxProductForm input[name^=varselid]" );
            for (var i = 0; i < aVarSelections.length; i++) {
                $( aVarSelections[i] ).attr( "value", "" );
            }
            $( "form.js-oxProductForm input[name=anid]" ).attr( "value", $( "form.js-oxProductForm input[name=parentid]" ).attr( "value" ) );
        },
            /**
         * Runs defined scripts inside the method, before ajax is called
             */
        _preAjaxCaller : function()
        {
            $('#zoomModal').remove();
        }
    }

    /**
     * Handles variant selection action
     * @returns {boolean}
     */
    function variantSelectActionHandler( e ) {
                var obj = $( this );
                // resetting
                if ( obj.parents().hasClass("js-disabled") ) {
            oxArticleVariant.resetVariantSelections();
                } else {
                    $( "form.js-oxProductForm input[name=anid]" ).attr( "value", $( "form.js-oxProductForm input[name=parentid]" ).attr( "value" ) );
                }

                // setting new selection
        if ( obj.parents('.js-fnSubmit').length > 0 ) {
            $('input:hidden', obj.parents('div.dropDown')).val( obj.data("selection-id") );

                    var form = $("form.js-oxWidgetReload");
                    $('input[name=fnc]', form).val("");
                    form.submit();
                }
        e.stopPropagation();
    }

            /**
     * Handles variant reset action
     * @returns {boolean}
             */
    function variantResetActionHandler( e ) {
        oxArticleVariant.resetVariantSelections();
                var form = $("form.js-oxWidgetReload");
                $('input[name=fnc]', form).val("");
                form.submit();
        e.stopPropagation();
    }

    /**
     * Handles form submit
     *
     * @returns {*}
     */
    function  formSubmit() {
        var aOptions = {}, target = $(this);
                if (!$("input[name='fnc']", this).val()) {
                    if (($( "input[name=aid]", this ).val() == $( "input[name=parentid]", this ).val() )) {
                        var aSelectionInputs = $("input[name^=varselid]", $("form.js-oxProductForm"));
                        if (aSelectionInputs.length) {
                            var sHash = '';
                    aSelectionInputs.not("*[value='']").each(function(i) {
                        sHash = sHash+i+':'+$(this).val()+"|";
                                aOptions[$(this).attr( "name" )] = $(this).val();
                            });
                    if ( jQuery.inArray( sHash, oxVariantSelections ) === -1 ) {
                        return oxArticleVariant.reload( $(target), $("#details"), $("#details")[0], aOptions);
                            }
                        }
                    }
            return oxArticleVariant.reload( $(target),$("#details"),$("#details")[0], aOptions);
                }
            }

    $.widget("ui.oxArticleVariant", oxArticleVariant );

})( jQuery );