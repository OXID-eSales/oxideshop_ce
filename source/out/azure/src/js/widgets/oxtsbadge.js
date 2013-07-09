/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 *
 * @link      http://www.oxid-esales.com
 * @package   out
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id: oxflyoutbox.js 35529 2011-05-23 07:31:20Z vilma $
 */
( function( $ ) {

    oxTsBadge = {

        options: {
            trustedShopId : "trustedShopId"
        },

        _create: function(){

            var self = this,
                options = self.options;

            var _ts = document.createElement('script');

            _ts.type = 'text/javascript';
            _ts.async = true;
            _ts.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widgets.trustedshops.com/js/'+options.trustedShopId+'.js';

            var __ts = document.getElementsByTagName('script')[0];
            __ts.parentNode.insertBefore(_ts, __ts);

        }
    }

    $.widget( "ui.oxTsBadge", oxTsBadge );

} )( jQuery );