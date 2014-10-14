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
 * Admin dyn trusted manager.
 * @package admin
 * @subpackage dyn
 */
class dyn_trusted_ratings extends Shop_Config
{
    /**
     * Config parameter which sould not be converted to multiline string
     *
     * @var array
     */
    protected $_aSkipMultiline = array( 'aTsLangIds', 'aHomeCountry', 'aTsActiveLangIds' );

    /**
     * Creates shop object, passes shop data to Smarty engine and returns name of
     * template file "dyn_trusted.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['oxid']    = $this->getConfig()->getShopId();
        $this->_aViewData["alllang"] = oxRegistry::getLang()->getLanguageArray();

        return "dyn_trusted_ratings.tpl";
    }

    /**
     * Saves changed shop configuration parameters.
     *
     * @return mixed
     */
    public function save()
    {
        $myConfig = $this->getConfig();
        $sOxId = $this->getEditObjectId();

        // base parameters
        $aConfStrs  = oxConfig::getParameter( "confstrs" );
        $aConfAArs  = oxConfig::getParameter( "confaarrs" );
        $aConfBools = oxConfig::getParameter( "confbools" );

        // validating language Ids
        if ( is_array( $aConfAArs['aTsLangIds'] ) ) {

            $blActive = ( isset( $aConfBools["blTsWidget"] ) && $aConfBools["blTsWidget"] == "true" ) ? true : false;
            $sPkg = "OXID_ESALES";

            $aActiveLangs = array();
            foreach ( $aConfAArs['aTsLangIds'] as $sLangId => $sId ) {
                $aActiveLangs[$sLangId] = false;
                if ( $sId ) {
                    $sTsUser = $myConfig->getConfigParam( 'sTsUser' );
                    $sTsPass = $myConfig->getConfigParam( 'sTsPass' );
                    // validating and switching on/off
                    $sResult = $this->_validateId( $sId, (bool) $blActive, $sTsUser, $sTsPass, $sPkg );

                    // keeping activation state
                    $aActiveLangs[$sLangId] = $sResult == "OK" ? true : false;

                    // error message
                    if ( $sResult && $sResult != "OK" ) {
                        $this->_aViewData["errorsaving"] = "DYN_TRUSTED_RATINGS_ERR_{$sResult}";
                    }
                }
            }

            $myConfig->saveShopConfVar( "arr", "aTsActiveLangIds", $aActiveLangs, $sOxId );
        }

        parent::save();
    }

    /**
     * Returns service wsdl url (test|regular) according to configuration
     *
     * @return string
     */
    protected function _getServiceWsdl()
    {
        $sWsdl = false;
        $oConfig = $this->getConfig();
        $aTsConfig = $oConfig->getConfigParam( "aTsConfig" );
        if ( is_array( $aTsConfig ) ) {
            $sWsdl = $aTsConfig["blTestMode"] ? $oConfig->getConfigParam( "sTsServiceTestWsdl" ) : $oConfig->getConfigParam( "sTsServiceWsdl" );
        }

        return $sWsdl;
    }

    /**
     * Validates Ts language id and returns validatsion status message
     *
     * @param string $sId      Trusted Shops Id
     * @param string $blActive Widget mode - active or not
     * @param string $sUser    Trusted Shops User name
     * @param string $sPass    Trusted Shops Password
     * @param string $sPkg     Package Name
     *
     * @return string | bool
     */
    protected function _validateId( $sId, $blActive, $sUser, $sPass, $sPkg )
    {
        $sReturn = false;
        if ( ( $sWsdl = $this->_getServiceWsdl() ) ) {
            try {
                $oClient = new SoapClient( $sWsdl );
                $sReturn = $oClient->updateRatingWidgetState( $sId, (int) $blActive, $sUser, $sPass, $sPkg );
            } catch ( SoapFault $oFault ) {
                $sReturn = $oFault->faultstring;
            }
        }
        return $sReturn;
    }

    /**
     * Returns view id ('dyn_interface')
     *
     * @return string
     */
    public function getViewId()
    {
        return 'dyn_interface';
    }

    /**
     * Converts Multiline text to simple array. Returns this array.
     *
     * @param string $sMultiline Multiline text or array
     *
     * @return array
     */
    protected function _multilineToArray( $sMultiline )
    {
        $aArr = $sMultiline;
        if ( !is_array( $aArr ) ) {
            $aArr = parent::_multilineToArray( $aArr );
        }
        return $aArr;
    }

    /**
     * Converts Multiline text to associative array. Returns this array.
     *
     * @param string $sMultiline Multiline text
     *
     * @return array
     */
    protected function _multilineToAarray( $sMultiline )
    {
        $aArr = $sMultiline;
        if ( !is_array( $aArr ) ) {
            $aArr = parent::_multilineToAarray( $aArr );
        }

        return $aArr;
    }
}
