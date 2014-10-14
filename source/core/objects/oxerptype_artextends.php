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


require_once 'oxerptype.php';

/**
 * Article extends type subclass
 */
class oxERPType_Artextends extends oxERPType
{
    /**
     * class constructor
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->_sTableName      = 'oxartextends';
    }
    
    /**
     * prepares object for saving in shop
     * returns true if save can proceed further
     *
     * @param oxBase $oShopObject shop object
     * @param array  $aData       data for importing
     *
     * @return boolean
     */
    protected function _preSaveObject($oShopObject, $aData)
    {
        return true;
    }

    /**
     * saves data by calling object saving
     *
     * @param array $aData               data for saving
     * @param bool  $blAllowCustomShopId allow custom shop id
     *
     * @return string | false
     */
    public function saveObject($aData, $blAllowCustomShopId)
    {
        $oShopObject = oxNew('oxi18n');
        $oShopObject->init('oxartextends');
        $oShopObject->setLanguage( 0 );
        $oShopObject->setEnableMultilang(false);

        foreach ($aData as $key => $value) {
            // change case to UPPER
            $sUPKey = strtoupper($key);
            if (!isset($aData[$sUPKey])) {
                unset($aData[$key]);
                $aData[$sUPKey] = $value;
            }
        }


        $blLoaded = false;
        if ($aData['OXID']) {
            $blLoaded = $oShopObject->load( $aData['OXID']);
        }

        $aData = $this->_preAssignObject( $oShopObject, $aData, $blAllowCustomShopId );

        if ($blLoaded) {
            $this->checkWriteAccess($oShopObject, $aData);
        } else {
            $this->checkCreateAccess($aData);
        }

        $oShopObject->assign( $aData );

        if ($blAllowCustomShopId) {
            $oShopObject->setIsDerived(false);
        }

        if ($this->_preSaveObject($oShopObject, $aData)) {
            // store
            if ( $oShopObject->save()) {
                return $this->_postSaveObject($oShopObject, $aData);
            }
        }

        return false;
    }    
}
