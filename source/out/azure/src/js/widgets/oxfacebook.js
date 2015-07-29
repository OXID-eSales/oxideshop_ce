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
    /*
     * Facebook related scripts
     */
    oxFacebook = {

        /*
         * FB widgets/buttons array
         */
        buttons: {
        },

        /*
         * Enables FB widgets
         */
        showFbWidgets: function ( sFbAppId, sLocale, sLoginUrl, sLogoutUrl ) {

            var self = this;
            self.key = null;

            for ( key in this.buttons ) {
                if ( this.buttons[key].script ) {
                    self.key = key;
                    $.getScript( this.buttons[key].script, function () {
                        $( self.key ).html( unescape( self.buttons[self.key].html ) );
                    } );
                } else {
                    $( key ).html( unescape( this.buttons[key].html ) );
                }
            }

            $.cookie( 'fbwidgetson',1, {path: '/'});
            this.fbInit( sFbAppId, sLocale, sLoginUrl, sLogoutUrl );
        },

        /*
         * Initing Facebook API
         *
         */
        fbInit: function ( sFbAppId, sLocale, sLoginUrl, sLogoutUrl ) {

            window.fbAsyncInit = function() {

                FB.init({appId: sFbAppId, status: true, cookie: true, xfbml: true, oauth: true});
                FB.Event.subscribe('auth.login', function(response) {
                    // redirecting after successfull login
                    setTimeout(function(){oxFacebook.redirectPage(sLoginUrl);}, 0);

                    if ( FB.XFBML.Host !== undefined && FB.XFBML.Host.parseDomTree )
                          setTimeout(function(){FB.XFBML.Host.parseDomTree;}, 0 );
                });

                FB.Event.subscribe('auth.logout', function(response) {
                    // redirecting after logout
                    setTimeout(function(){oxFacebook.redirectPage(sLogoutUrl);}, 0);
                });
            };

            // loading FB script file
            var e   = document.createElement('script');
            e.type  = 'text/javascript';
            e.async = true;
            e.src   = document.location.protocol + '//connect.facebook.net/' + sLocale + '/all.js';
            $('#fb-root').append(e);
        },

        /*
         * Redicrecting page to given url
         */
        redirectPage: function ( sUrl ) {

           sUrl = sUrl.toString().replace(/&amp;/g,"&");
           document.location.href = sUrl;
        },

        /*
         * Add scripts from tpl
         */
        initDetailsPagePartial : function () {
            if (window.fbAsyncInit) {
                window.fbAsyncInit();
            }
        }

    };

