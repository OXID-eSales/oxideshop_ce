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

    /**
     * Ajax
     */
    oxAjax = {

        /**
         * Loading temporary screen when ajax call proseeds
         */
        loadingScreen:  {

            /**
             * Starts load
             *
             * @target - DOM element witch must be hide with the loading screen
             * @iconPositionElement - element of a target on witch loaging icon is shown
             */
            start : function (target, iconPositionElement) {

                var loadingScreens = Array();
                $(target).each(function() {
                    var overlayKeeper = document.createElement("div");
                    overlayKeeper.innerHTML = '<div class="loadingfade"></div><div class="loadingicon"></div><div class="loadingiconbg"></div>';
                    $('div', overlayKeeper).css({
                            'position' : 'absolute',
                            'left'     : $(this).offset().left-10,
                            'top'      : $(this).offset().top-10,
                            'width'    : $(this).width()+20,
                            'height'   : $(this).height()+20
                        });
                    if (iconPositionElement && iconPositionElement.length) {
                        var x = Math.round(
                            iconPositionElement.offset().left // my left
                            - 10 - $(this).offset().left      // relativeness
                            + iconPositionElement.width()/2   // plus half of width to center
                        );
                        var offsetTop = iconPositionElement.offset().top;
                        var y = Math.round(
                            offsetTop                         //my top
                            - 10 - $(this).offset().top       // relativeness
                            + (                               // this requires, that last element in collection, would be the bottom one
                                                              // as it computes last element offset from the first one plus its height
                                iconPositionElement.last().offset().top - offsetTop + iconPositionElement.last().height()
                            )/2
                        );

                        $('div.loadingiconbg, div.loadingicon', overlayKeeper).css({
                            'background-position' : x + "px "+y+"px"
                        });
                    }
                    $('div.loadingfade', overlayKeeper)
                        .css({'opacity' : 0})
                        .animate({
                            opacity: 0.55
                        }, 200
                        );
                    $("body").append(overlayKeeper);
                    loadingScreens.push(overlayKeeper);
                });

                return loadingScreens;
            },


            /**
             * Stops viewing loading screens
             *
             * @loadingScreens - one or more showing screens
             */
            stop : function ( loadingScreens ) {
              $.each(loadingScreens, function(i, el) {
                  $('div', el).not('.loadingfade').remove();
                  $('div.loadingfade', el)
                      .stop(true, true)
                      .animate({
                          opacity: 0
                      }, 100, function(){
                          $(el).remove();
                      });
              });
            }
        },

        /**
         * Updating errors on page
         *
         * @errors - array of errors
         */
        updatePageErrors : function(errors) {
            if (errors.length) {
                var errlist = $("#content > .status.error");
                if (errlist.length == 0) {
                    $("#content").prepend("<div class='status error corners'>");
                    errlist = $("#content > .status.error");
                }
                if (errlist) {
                    errlist.children().remove();
                    var i;
                    for (i=0; i<errors.length; i++) {
                        var p = document.createElement('p');
                        $(p).append(document.createTextNode(errors[i]));
                        errlist.append(p);
                    }
                }
            } else {
                $("#content > .status.error").remove();
            }
        },

        /**
         * Ajax call
         *
         * @activator - link or form element that activates ajax call
         * @params - call params: targetEl, iconPosEl, onSuccess, onError, additionalData
         */
        ajax : function(activator, params) {
            var self = this;
            var inputs = {};
            var action = "";
            var type   = "";
            if (activator[0].tagName == 'FORM') {
                $("input", activator).each(function() {
                    if (this.type == 'checkbox' && !this.checked) return true;
                    inputs[this.name] = this.value;
                });
                action = activator.attr("action");
                type   = activator.attr("method");
            } else if (activator[0].tagName == 'A') {
                action = activator.attr("href");
            }

            if (params['additionalData']) {
                $.each(params['additionalData'], function(i, f) {inputs[i] = f;});
            }

            // sorting array to pass parameters alphabetically
            var aInputs = {};
            var keys = Array();
            for ( var key in inputs ) {
                if ( inputs.hasOwnProperty( key ) ) {
                    keys.push( key );
                }
            }
            keys.sort().forEach( function( i ) { aInputs[i] = inputs[i]; } )

            var sLoadingScreen = null;
            if (params['targetEl']) {
                sLoadingScreen = self.loadingScreen.start(params['targetEl'], params['iconPosEl']);
            }

            if (!type) {
                type = "get";
            }

            jQuery.ajax({
                data    : aInputs,
                url     : action,
                type    : type,
                timeout : 30000,
                beforeSend: function( jqXHR, settings ) {
                    settings.url = settings.url.replace( "&&", "&" );
                },
                error   : function(jqXHR, textStatus, errorThrown) {
                    if (sLoadingScreen) {
                        self.loadingScreen.stop(sLoadingScreen);
                    }
                    if (params['onError']) {
                        params['onError'](jqXHR, textStatus, errorThrown);
                    }
                },

                success : function(r) {

                    if (sLoadingScreen) {
                        self.loadingScreen.stop(sLoadingScreen);
                    }
                    if (r['debuginfo'] != undefined && r['debuginfo']) {
                        $("body").append(r['debuginfo']);
                    }
                    if   (r['errors'] != undefined
                       && r['errors']['default'] != undefined) {
                        self.updatePageErrors(r['errors']['default']);
                    } else {
                        self.updatePageErrors([]);
                    }
                    if (params['onSuccess']) {
                        params['onSuccess'](r, inputs);
                    }
                }
            });
        },

        /**
         * If it's possible report JS error
         *
         * @param e JS exception
         */
        reportJSError: function(e) {
            if (typeof console != 'undefined' && typeof console.error != 'undefined') {
                console.error(e);
            }
        },

        /**
         * Evals returned html and executes javascript after reload
         *
         * @container - witch javascript must be restarted
         */
        evalScripts : function(container){
            var self = this;
            try {
                $("script", container).each(function(){
                    try {
                        if (this.src != '' && $('body > script[src="'+this.src+'"]').length == 0) {
                            $('body').append(this);
                            document.body.appendChild(this);
                            return true;
                        }
                        eval(this.innerHTML);
                    } catch (e) {
                       self.reportJSError(e);
                    }
                    $(this).remove();
                });
            } catch (e) {
               self.reportJSError(e);
            }
        }
    };

    $.widget("ui.oxAjax", oxAjax );

})( jQuery );
