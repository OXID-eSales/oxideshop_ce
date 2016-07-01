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
( function ( $ ) {

    var oxArticleVariant = {

        /**
         * Initiating article variants selector
         * @private
         */
        _create: function() {
            $("ul.vardrop a").click( variantSelectActionHandler );
            $('div.variantReset a').click( variantResetActionHandler );

            $("form.js-oxWidgetReload").submit( formSubmit );
            $("form.js-oxProductForm").submit( formSubmit );
        },

        /**
         * Reloads block
         *
         * @param activator
         * @param highlightTargets
         * @param contentTarget
         * @param aOptions
         * @returns {boolean}
         */
        reload: function(activator, highlightTargets, contentTarget, aOptions) {
            preAjaxCaller();
            oxAjax.ajax(
                activator, {//targetEl, onSuccess, onError, additionalData
                    'targetEl'  : highlightTargets,
                    'iconPosEl' : $("#variants").find(".dropDown"),
                    'additionalData' : aOptions,
                    'onSuccess' : function(r) {
                        $( contentTarget ).html( r );
                        if ( typeof WidgetsHandler !== 'undefined') {
                            WidgetsHandler.reloadWidget('oxwarticledetails');
                            WidgetsHandler.reloadWidget('oxwrating');
                            WidgetsHandler.reloadWidget('oxwreview');
                        } else {
                            oxAjax.evalScripts(contentTarget);
                        }
                    }
                }
            );
            return false;
        },

        /**
         * Resets all variant selections
         */
        resetVariantSelections: function() {
            resetVariantSelections();
        }
    };

    /**
     * Handles variant selection action
     * @returns {boolean}
     */
    function variantSelectActionHandler( e ) {
        var obj = $( this );
        // resetting
        if ( obj.parents().hasClass("js-disabled") ) {
            resetVariantSelections();
        } else {
            var productForm = $('form.js-oxProductForm');
            productForm.find('input[name=anid]').attr('value', productForm.find('input[name=parentid]').attr('value'));
        }

        // setting new selection
        if ( obj.parents('.js-fnSubmit').length > 0 ) {
            $('input:hidden', obj.parents('div.dropDown')).val( obj.data("selection-id") );

            var form = $("form.js-oxWidgetReload");
            $('input[name=fnc]', form).val("");
            form.submit();
        }
        return false;
    }

    /**
     * Handles variant reset action
     * @returns {boolean}
     */
    function variantResetActionHandler( e ) {
        resetVariantSelections();
        var form = $("form.js-oxWidgetReload");
        $('input[name=fnc]', form).val("");
        form.submit();
        return false;
    }

    /**
     * Resets variant selections
     */
    function resetVariantSelections() {
        var productForm = $('form.js-oxProductForm');
        var aVarSelections = productForm.find('input[name^=varselid]').add('form.js-oxWidgetReload input[name^=varselid]');
        aVarSelections.attr('value', '');
        productForm.find('input[name=anid]').attr('value', productForm.find('input[name=parentid]').attr('value'));
    }

    /**
     * Handles form submit
     *
     * @returns {*}
     */
    function  formSubmit() {
        var aOptions = {}, target = $(this);
        if (!$("input[name='fnc']", this).val()) {
            var detailsContainer = $('#details_container');
            
            if (($( "input[name=aid]", this ).val() == $( "input[name=parentid]", this ).val() )) {
                var aSelectionInputs = $("input[name^=varselid]", $("form.js-oxProductForm"));
                if (aSelectionInputs.length) {
                    var sHash = '';
                    aSelectionInputs.each(function(i) {
                        sHash = sHash+i+':'+$(this).val()+"|";
                        aOptions[$(this).attr( "name" )] = $(this).val();
                    });
                }
            }
            return oxArticleVariant.reload( $(target), detailsContainer, detailsContainer[0], aOptions);
        }
    }

    /**
     * Runs defined scripts inside the method, before ajax is called
     */
    function preAjaxCaller() {
        $('#zoomModal').remove();
    }

    $.widget("ui.oxArticleVariant", oxArticleVariant );

})( jQuery );
