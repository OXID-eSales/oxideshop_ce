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
 * @package   EBL
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

abstract class EBLPortlet
{
    /**
     * Efire login
     * @var string
     */
    protected $_sEfiLogin;

    /**
     * Efire password
     * @var string
     */
    protected $_sEfiPassword;

    /**
     * EFI soap client onstance
     * @var eblsoapclient
     */
    protected $_oEfiClient;

    /**
     * Efire wsdl url
     * @var string
     */
    protected $_sWsdl = "https://soap.oxid-efire.com/wsdl/";

    /**
     * Portlet status - TRUE - On, FALSE - Off
     * @var bool
     */
    protected $_blPortletEnabled;

    /**
     * Portlet settings array
     * @var array
     */
    protected $_aSettings;

    /**
     * Portlet config parameters cache
     * @var array
     */
    protected $_aParamCache = array();

    /**
     * Portlet config cache lifetime in seconds
     * @var int
     */
    protected $_iPortletConfigCacheLifetime = 3600;

    /**
     * Transaction (session) id
     * @var string
     */
    protected $_sTransactionId;

    /**
     * EBL Version number
     * @var string
     */
    protected $_sEBLVersion = '1.0';

    /**
     * Portlet Version number
     * @var string
     */
    protected $_sPortletVersion;

    /**
     * Returns portlet name
     *
     * @return string
     */
    abstract protected function _getPortletName();

    /**
     * Returns user agent info
     *
     * @param string $sInfo additional info you may want to append
     *
     * @return string
     */
    abstract protected function _getUserAgent( $sInfo = null );

    /**
     * Get parser, able to convert XML to settings associative array.
     *
     * @return EBLDataParser
     */
    abstract protected function _getSettingsParser();

    /**
     * Returns EFI login
     *
     * @return string
     */
    protected function _getEfiLogin()
    {
        return $this->_sEfiLogin;
    }

    /**
     * Returns EBL library version number
     *
     * @return string
     */
    protected function _getEBLVersion()
    {
        return $this->_sEBLVersion;
    }

    /**
     * Returns module version number
     *
     * @return string
     */
    protected function _getPortletVersion()
    {
        return $this->_sPortletVersion;
    }

    /**
     * Returns transaction id
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->_sTransactionId;
    }

    /**
     * Transaction id setter
     *
     * @param string $sTransId transaction id
     *
     * @return null
     */
    public function setTransactionId( $sTransId )
    {
        $this->_sTransactionId = $sTransId;
    }

    /**
     * Returns EFI passwword
     *
     * @return string
     */
    protected function _getEfiPassword()
    {
        return $this->_sEfiPassword;
    }

    /**
     * Class constructor
     *
     * @param string $sEfiLogin    efire user login name
     * @param string $sEfiPassword efire user password
     * @param string $sEfiWsdlUrl  wsdl url (optional)
     *
     * @return null
     */
    public function __construct( $sEfiLogin, $sEfiPassword, $sEfiWsdlUrl = null )
    {
        $this->_sEfiLogin    = $sEfiLogin;
        $this->_sEfiPassword = $sEfiPassword;

        if ( $sEfiWsdlUrl !== null ) {
            $this->_sWsdl = $sEfiWsdlUrl;
        }
    }

    /**
     * Returns efire wsdl url
     *
     * @return string
     */
    protected function _getWsdl()
    {
        return $this->_sWsdl;
    }

    /**
     * Creates and returns efi soap client
     *
     * @param string $sActionName name of action to be executed
     *
     * @return EBLSOAPClient
     */
    protected function _getClient( $sActionName )
    {
        /**
         * Due to strange and unreliable soap client/server behaviour
         * cant create and keep single soap client instance and have
         * to create new one for each call
         */
        try {
            $aParams["login"]      = $this->_getEfiLogin();
            $aParams["password"]   = $this->_getEfiPassword();
            $aParams['user_agent'] = $this->_getUserAgent( $sActionName );

            $oEfiClient = oxNew( "EBLSoapClient", $this->_getWsdl(), $aParams );
        } catch ( Exception $oExcp ) {
            // error..
            $oEfiClient = null;
        }

        return $oEfiClient;
    }

    /**
     * Loading settings from efire into $_aSettings array
     *
     * @return array
     */
    protected function _getSettings()
    {
        // cached?
        $this->_aSettings = $this->_getFromCache( "_aSettings" );
        if ( $this->_aSettings === null ) {
            $this->_aSettings = false;

            if ( ( $sName = $this->_getPortletName() ) &&
                 ( $oClient = $this->_getClient( "getSettings" ) ) ) {
                try {
                    $oResult = $oClient->getSettings();
                    if ( isset( $oResult->blResult ) && $oResult->blResult == 1 && isset( $oResult->sMessage ) ) {
                        $oXML = simplexml_load_string($oResult->sMessage);
                        $this->_aSettings = $this->_getSettingsParser()->getAsocArray($oXML);
                    }
                } catch ( Exception $oExcp ) {
                }
            }

            // write to cache
            $this->_writeToCache( "_aSettings", $this->_aSettings, "aarr" );
        }
        return $this->_aSettings;
    }

    /**
     * Calls to efire service and checks if protlet is enabled..
     *
     * @return bool
     */
    public function isPortletEnabled()
    {
        // cached?
        $this->_blPortletEnabled = $this->_getFromCache( "_blPortletEnabled" );
        if ( $this->_blPortletEnabled === null ) {
            $this->_blPortletEnabled = false;
            if( ( $sName = $this->_getPortletName() ) &&
                ( $oClient = $this->_getClient( "isPortletEnabled" ) ) ) {
                try {
                    $oResult = $oClient->isPortletEnabled( $sName );
                    $this->_blPortletEnabled = isset( $oResult->blResult ) ? (bool) $oResult->blResult : false;
                } catch ( Exception $oExcp ) {
                    $this->_blPortletEnabled = null;
                }
            }

            // write to cache
            $this->_writeToCache( "_blPortletEnabled", $this->_blPortletEnabled );
        }
        return $this->_blPortletEnabled;
    }

    /**
     * Returns efire parameter value
     *
     * @param string $sParamName parameter name
     *
     * @return string
     */
    public function getParameter( $sParamName )
    {
        // fetching settings..
        $aSettings = $this->_getSettings();
        if ( $aSettings && array_key_exists( $sParamName, $aSettings ) ) {
            return $aSettings[$sParamName];
        }

        return null;
    }

    /**
     * Returns config parameter name (portlet name + "_" + parameter name)
     *
     * @param string $sName
     *
     * @return string
     */
    protected function _getParamKeyName( $sName )
    {
        return $this->_getPortletName() . $sName;
    }

    /**
     * Returns TRUE if portlet config cache is expired
     *
     * @return bool
     */
    protected function _isCacheExpired()
    {
        // looking for cache expiration data..
        $sKeyName = $this->_getParamKeyName( "settingscachevalidto" );
        return time() >= (int) $this->_getConfig()->getShopConfVar( $sKeyName, $this->_getShopId() );
    }

    /**
     * Returns active shop id
     *
     * @return string
     */
    protected function _getShopId()
    {
        return $this->_getConfig()->getShopId();
    }

    /**
     * Returns oxconfig instance
     *
     * @return oxconfig
     */
    protected function _getConfig()
    {
        return oxConfig::getInstance();
    }

    /**
     * Returns cached parameter value is possible (checks internal cache or loads from db and stores to cache)
     *
     * @param string $sName parameter name
     *
     * @return mixed
     */
    protected function _getFromCache( $sName )
    {
        $sKeyName = $this->_getParamKeyName( $sName );
        $sValue   = null;

        if ( array_key_exists( $sKeyName, $this->_aParamCache ) ) {
            // loading from internal cache
            $sValue = $this->_aParamCache[$sKeyName];
        } elseif ( !$this->_isCacheExpired() ) {
            // loading from db
            $sValue = $this->_getConfig()->getShopConfVar( $sKeyName, $this->_getShopId() );
        }

        return $sValue;
    }

    /**
     * Returns settings cache life time value
     *
     * @return int
     */
    protected function _getSettingsCacheLifeTime()
    {
        return $this->_iPortletConfigCacheLifetime;
    }

    /**
     * Writes given parameter to cache (internal and db)
     *
     * @param string $sName  parameter name
     * @param midex  $mValue parameter value
     * @param string $sType  parameter type
     *
     * @return null
     */
    protected function _writeToCache( $sName, $mValue, $sType = "str" )
    {
        $iShopId  = $this->_getShopId();
        $sKeyName = $this->_getParamKeyName( $sName );

        // storing internally
        $this->_aParamCache[$sKeyName] = $mValue;

        // writing into db
        $this->_getConfig()->saveShopConfVar( $sType, $sKeyName, $mValue, $iShopId );

        // updating time until cache is valid
        $sKeyName = $this->_getParamKeyName( "settingscachevalidto" );
        $this->_getConfig()->saveShopConfVar( "str", $sKeyName, time() + $this->_getSettingsCacheLifeTime(), $iShopId );
    }
}
