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
 * Admin order address manager.
 * Collects order addressing information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> Address.
 * @package admin
 */
class Order_Address extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxorder object
     * and passes it's data to Smarty engine. Returns name of template
     * file "order_address.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oOrder = oxNew( "oxorder" );
            $oOrder->load( $soxId);

            $this->_aViewData["edit"] =  $oOrder;
        }

        $oCountryList = oxNew( "oxCountryList" );
        $oCountryList->loadActiveCountries( oxRegistry::getLang()->getObjectTplLanguage() );

        $this->_aViewData["countrylist"] = $oCountryList;

        return "order_address.tpl";
    }

    /**
     * Iterates through data array, checks if specified fields are filled
     * in, cleanups not needed data
     *
     * @param array  $aData          data to process
     * @param string $sTypeToProcess data type to process e.g. "oxorder__oxdel"
     * @param array  $aIgnore        fields which must be ignored while processing
     *
     * @return null
     */
    protected function _processAddress( $aData, $sTypeToProcess, $aIgnore )
    {
        // empty address fields?
        $blEmpty = true;

        // here we will store names of fields which needs to be cleaned up
        $aFields = array();

        foreach ( $aData as $sName => $sValue ) {

            // if field type matches..
            if ( strpos( $sName, $sTypeToProcess ) !== false ) {

                // storing which fields must be unset..
                $aFields[] = $sName;

                // ignoring whats need to be ignored and testing values
                if ( !in_array( $sName, $aIgnore ) && $sValue ) {

                    // something was found - means leaving as is..
                    $blEmpty = false;
                    break;
                }
            }
        }

        // cleanup if empty
        if ( $blEmpty ) {
            foreach ( $aFields as $sName ) {
                $aData[$sName] = "";
            }
        }

        return $aData;
    }

    /**
     * Saves ordering address information.
     *
     * @return string
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = (array) oxConfig::getParameter( "editval");

            //TODO check if shop id is realy necessary at this place.
            $sShopID = oxSession::getVar( "actshop" );
            $aParams['oxorder__oxshopid'] = $sShopID;

        $oOrder = oxNew( "oxorder" );
        if ( $soxId != "-1") {
            $oOrder->load( $soxId );
        } else {
            $aParams['oxorder__oxid'] = null;
        }

        $aParams = $this->_processAddress( $aParams, "oxorder__oxdel", array( "oxorder__oxdelsal" ) );
        $oOrder->assign( $aParams );
        $oOrder->save();

        // set oxid if inserted
        $this->setEditObjectId( $oOrder->getId() );
    }
}
