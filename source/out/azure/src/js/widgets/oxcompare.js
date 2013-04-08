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
 * @version   SVN: $Id: oxcompare.js 35529 2011-05-23 07:31:20Z vilma $
 */
( function ( $ ) {

    /**
     * Compare list
     */
    oxCompare = {
        options: {
            browserMozzila  : "mozilla",
            browserIE       : "msie",
            propertyHeight  : "height",
            classFirstCol   : ".js-firstCol",
            idDataTable     : "#compareDataDiv",
            elementTd       : "td",
            idFirstTr       : "#firstTr"
        },

        _create: function() {

            var self = this;
            var options = self.options;
            var iColumnCount = self.getColumnCount();
            var sBrowser = self.getBrowser();

            self.alignRows(sBrowser, iColumnCount);

            if ( $( options.idDataTable ).length ) {
                $( options.idDataTable ).jScrollPane({
                    showArrows: true,
                    horizontalGutter: 0
                });
            }
        },

        /**
         * align first columns rows with data columns
         *
         * @return object
         */
        alignRows: function(sBrowser, iColumnCount)
        {
            var iNumberOfRow = 0;
            var self = this;
            var options = this.options;

            $(self.options.classFirstCol).each(function(i){

                  var oFirstColumn = $(this);
                  var oOtherColumn = self.getOtherColumn(iColumnCount, iNumberOfRow);

                  var firstColumnHeight = self.getColumnHeight(sBrowser, oFirstColumn);
                  var otherColumnHeight = self.getColumnHeight(sBrowser, oOtherColumn);

                  if(firstColumnHeight >  otherColumnHeight){
                    self.setColumnHeight(oOtherColumn, firstColumnHeight );
                    self.setColumnHeight(oFirstColumn, firstColumnHeight );
                  }else{
                    self.setColumnHeight(oFirstColumn, otherColumnHeight);
                    self.setColumnHeight(oOtherColumn, otherColumnHeight );
                  }

                  iNumberOfRow++;
          });

        },

        /**
         * get colummns rows hight
         *
         * @return integer
         */
        getColumnHeight: function(sBrowser, oColumn)
        {
            if(sBrowser == this.options.browserMozzila){
                return oColumn.outerHeight();
            }
            else if(sBrowser == this.options.browserIE){
                return oColumn.innerHeight();
            }
            else {
                return oColumn.height();
            }
        },

        /**
         * set colummns rows hight
         *
         * @return object
         */
        setColumnHeight: function(oColumn, iHeight)
        {
            return $(oColumn).css(this.options.propertyHeight, iHeight);
        },

        /**
         * get colummns
         *
         * @return object
         */
        getOtherColumn: function(iColumnCount, iNumberOfRow)
        {
            return $( this.options.idDataTable + ' ' + this.options.elementTd + ':eq(' + iColumnCount * iNumberOfRow + ')');
        },

        /**
         * get browser
         *
         * @return object
         */
        getBrowser: function(){

            var sBrowser = this.options.browserMozzila;

            jQuery.each( jQuery.browser, function( i, val ) {
                if ( val == true ){
                   sBrowser = i.toString();
                 }
             });

            return sBrowser;
        },

        /**
         * get column Count
         *
         * @return object
         */
        getColumnCount: function()
        {
            return $( this.options.idFirstTr + '>' + this.options.elementTd).length;
        }
    };

    /**
     * Compare list widget
     */
    $.widget("ui.oxCompare", oxCompare );

})( jQuery );
