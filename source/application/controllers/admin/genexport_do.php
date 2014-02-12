<?php
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

/**
 * General export class.
 * @package admin
 */
class GenExport_Do extends DynExportBase
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo       = "genExport_do";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain     = "genExport_main";

    /**
     * Export file name
     *
     * @var string
     */
    public $sExportFileName = "genexport";

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = "dynbase_do.tpl";

    /**
     * Does Export line by line on position iCnt
     *
     * @param integer $iCnt export position
     *
     * @return bool
     */
    public function nextTick( $iCnt )
    {
        $iExportedItems = $iCnt;
        $blContinue = false;
        if ( $oArticle = $this->getOneArticle( $iCnt, $blContinue ) ) {
            $myConfig = oxRegistry::getConfig();
            $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
            $oSmarty->assign( "sCustomHeader", oxSession::getVar("sExportCustomHeader") );
            $oSmarty->assign_by_ref( "linenr", $iCnt );
            $oSmarty->assign_by_ref( "article", $oArticle );
            $oSmarty->assign( "spr", $myConfig->getConfigParam( 'sCSVSign' ) );
            $oSmarty->assign( "encl", $myConfig->getConfigParam( 'sGiCsvFieldEncloser' ) );
            $this->write( $oSmarty->fetch( "genexport.tpl", $this->getViewId() ) );
            return ++$iExportedItems;
        }

        return $blContinue;
    }

    /**
     * writes one line into open export file
     *
     * @param string $sLine exported line
     *
     * @return null
     */
    public function write( $sLine )
    {
        $sLine = $this->removeSID( $sLine );

        $sLine = str_replace( array("\r\n","\n"), "", $sLine );
        $sLine = str_replace( "<br>", "\n", $sLine );

        fwrite( $this->fpFile, $sLine."\r\n");
    }

}
