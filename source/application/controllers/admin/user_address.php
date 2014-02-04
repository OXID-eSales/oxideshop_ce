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
 * Admin user address setting manager.
 * Collects user address settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Addresses.
 * @package admin
 */
class User_Address extends oxAdminDetails
{
    /**
     * If true, means that address was deleted
     *
     * @var bool
     */
    protected $_blDelete = false;

    /**
     * Executes parent method parent::render(), creates oxuser and oxbase objects,
     * passes data to Smarty engine and returns name of template file
     * "user_address.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oUser = oxNew( "oxuser" );
            $oUser->load( $soxId);

            // load adress
            $soxAddressId = isset($this->sSavedOxid)?$this->sSavedOxid:oxConfig::getParameter( "oxaddressid");
            if ( $soxAddressId != "-1" && isset( $soxAddressId ) ) {
                $oAdress = oxNew( "oxaddress" );
                $oAdress->load( $soxAddressId );
                $this->_aViewData["edit"] = $oAdress;
            }

            $this->_aViewData["oxaddressid"] = $soxAddressId;

            // generate selected
            $oAddressList = $oUser->getUserAddresses();
            foreach ( $oAddressList as $oAddress ) {
                if ( $oAddress->oxaddress__oxid->value == $soxAddressId ) {
                    $oAddress->selected = 1;
                    break;
                }
            }

            $this->_aViewData["edituser"] = $oUser;
        }

        $oCountryList = oxNew( "oxCountryList" );
        $oCountryList->loadActiveCountries( oxRegistry::getLang()->getObjectTplLanguage() );

        $this->_aViewData["countrylist"] = $oCountryList;

        if (!$this->_allowAdminEdit($soxId))
            $this->_aViewData['readonly'] = true;

        return "user_address.tpl";
    }

    /**
     * Saves user addressing information.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        if ( $this->_allowAdminEdit( $this->getEditObjectId() ) ) {
            $aParams = oxConfig::getParameter( "editval" );
            $oAdress = oxNew( "oxaddress" );
            if ( isset( $aParams['oxaddress__oxid'] ) && $aParams['oxaddress__oxid'] == "-1" ) {
                $aParams['oxaddress__oxid'] = null;
            } else {
                $oAdress->load( $aParams['oxaddress__oxid'] );
            }

            $oAdress->assign( $aParams );
            $oAdress->save();

            $this->sSavedOxid = $oAdress->getId();
        }
    }

    /**
     * Deletes user addressing information.
     *
     * @return null
     */
    public function delAddress()
    {
        $this->_blDelete = false;
        if ( $this->_allowAdminEdit( $this->getEditObjectId() ) ) {
            $aParams = oxConfig::getParameter( "editval" );
            if ( isset( $aParams['oxaddress__oxid'] ) && $aParams['oxaddress__oxid'] != "-1" ) {
                $oAdress = oxNew( "oxaddress" );
                $this->_blDelete = $oAdress->delete( $aParams['oxaddress__oxid'] );
            }
        }
    }
}
