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

    oxArticleVariant = {

        _create: function() {

            var self = this;

            /**
             * Variant selection dropdown
             */
            $("ul.vardrop a").click(function() {

                var obj = $( this );
                self.reselect();
                // resetting
                if ( obj.parents().hasClass("js-disabled") ) {
                    self.resetVariantSelections();
                } else {
                    $( "form.js-oxProductForm input[name=anid]" ).attr( "value", $( "form.js-oxProductForm input[name=parentid]" ).attr( "value" ) );
                }

                // setting new selection
                if ( obj.parents().hasClass("js-fnSubmit") ){
                    obj.parent('li').parent('ul').siblings('input:hidden').attr( "value", obj.attr("data-selection-id") );

                    var form = $("form.js-oxWidgetReload");
                    $('input[name=fnc]', form).val("");
                    form.submit();
                }
                return false;
            });

            /**
             * variant reset link
             */
            $('div.variantReset a').click( function () {
                self.resetVariantSelections();
                var form = $("form.js-oxWidgetReload");
                $('input[name=fnc]', form).val("");
                form.submit();
                return false;
            });
            function submitFunc( target ) {
                var aOptions = {};
                if (!$("input[name='fnc']", this).val()) {
                    if (($( "input[name=aid]", this ).val() == $( "input[name=parentid]", this ).val() )) {
                        var aSelectionInputs = $("input[name^=varselid]", $("form.js-oxProductForm"));
                        if (aSelectionInputs.length) {
                            var hash = '';
                            var sHash = '';
                            aSelectionInputs.not("*[value='']").each(function(i){
                                hash = hash+i+':'+$(this).val()+"|";
                                aOptions[$(this).attr( "name" )] = $(this).val();
                                sHash = $(this).val();
                            });
                            if ( jQuery.inArray( hash, oxVariantSelections ) === -1 ) {
                                return self.reload( $(target), $("#details"), $("#details")[0], aOptions);
                            }
                        }
                    }
                    return self.reload( $(target),$("#details"),$("#details")[0], aOptions);
                }
            }
            $("form.js-oxWidgetReload").submit(function () { return submitFunc( "form.js-oxWidgetReload" ) });
            $("form.js-oxProductForm").submit(function() { submitFunc( "form.js-oxProductForm" ) });
        },

        /**
         * Select actually selected options, and not the ones from browser cache
         */
        reselect: function() {
            var obj = $("input[name^=varselid]");
            obj.each( function() {
                var selected = this.parentNode.querySelector( ".selected" );
                if ( selected !== null ) {
                    this.value = selected.getAttribute("data-selection-id"); // not jquery, javascript !
                } else {
                    this.value = "";
                }
            });
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
                        init_oxwdetailspage();
                    }
                }
            );
            return false;
        },
        resetVariantSelections : function()
        {
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
    $.widget("ui.oxArticleVariant", oxArticleVariant );

})( jQuery );