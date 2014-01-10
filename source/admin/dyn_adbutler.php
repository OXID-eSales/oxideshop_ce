<?php
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
 * @package   admin
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

/**
 * Admin dyn adbutler manager.
 * @package admin
 * @subpackage dyn
 */
class dyn_adbutler extends dyn_interface
{
    /**
     * Creates shop object, passes shop data to Smarty engine and returns name of
     * template file "dyn_adbutler.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oShop = oxNew( "oxshop" );
        $oShop->load( oxSession::getVar( "actshop") );
        $this->_aViewData["edit"] = $oShop;

        return "dyn_adbutler.tpl";
    }

    /**
     * Saves service attributes.
     *
     * @return null
     */
    public function save()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

        $oShop = oxNew( "oxshop" );
        $oShop->load( $soxId);

        $aParams['oxshops__oxadbutlerid'] = $this->_checkId($aParams['oxshops__oxadbutlerid']);

        //$aParams = $oShop->ConvertNameArray2Idx( $aParams);
        $oShop->assign( $aParams);

        $oShop->save();
    }

    /**
     * Checks if entered id has 9 numbers, if not adds zeros to the front.
     *
     * @param string $sProgrammId programm id
     *
     * @return string
     */
    public function _checkId( $sProgrammId )
    {
        $iIdLen = strlen( $sProgrammId );
        if ( $iIdLen < 9 ) {
            $sProgrammId = str_repeat( "0", 9-$iIdLen ) . $sProgrammId;
        }
        return $sProgrammId;
    }
}
