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
 * Super config class
 * @package core
 */
class oxSuperCfg
{
    /**
     * oxconfig instance
     *
     * @var oxconfig
     */
    protected static $_oConfig = null;

    /**
     * oxsession instance
     *
     * @var oxsession
     */
    protected static $_oSession = null;

    /**
     * oxrights instance
     *
     * @var oxrights
     */
    protected static $_oRights = null;

    /**
     * oxuser object
     *
     * @var oxuser
     */
    protected static $_oActUser = null;

    /**
     * Admin mode marker
     *
     * @var bool
     */
    protected static $_blIsAdmin = null;

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessable in current class
     *
     * @return string
     */
    public function __call( $sMethod, $aArgs )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( substr( $sMethod, 0, 4) == "UNIT" ) {
                $sMethod = str_replace( "UNIT", "_", $sMethod );
            }
            if ( method_exists( $this, $sMethod)) {
                return call_user_func_array( array( & $this, $sMethod ), $aArgs );
            }
        }

        throw new oxSystemComponentException( "Function '$sMethod' does not exist or is not accessible! (" . get_class($this) . ")".PHP_EOL);
    }

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * oxConfig instance getter
     *
     * @return oxconfig
     */
    public function getConfig()
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( $this->unitCustModConf ) ) {
                return $this->unitCustModConf;
            }
            return oxRegistry::getConfig();
        }

        if ( self::$_oConfig == null ) {
            self::$_oConfig = oxRegistry::getConfig();
        }

        return self::$_oConfig;
    }

    /**
     * oxConfig instance setter
     *
     * @param oxconfig $oConfig config object
     *
     * @return null
     */
    public function setConfig( $oConfig )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            $this->unitCustModConf = $oConfig;
            return;
        }

        self::$_oConfig = $oConfig;
    }

    /**
     * oxSession instance getter
     *
     * @return oxsession
     */
    public function getSession()
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( $this->unitCustModSess ) ) {
                return $this->unitCustModSess;
            }
            return oxRegistry::getSession();
        }

        if ( self::$_oSession == null ) {
            self::$_oSession = oxRegistry::getSession();
        }

        return self::$_oSession;
    }

    /**
     * oxSession instance setter
     *
     * @param oxsession $oSession session object
     *
     * @return null
     */
    public function setSession( $oSession )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            $this->unitCustModSess = $oSession;
            return;
        }

        self::$_oSession = $oSession;
    }

    /**
     * Active user getter
     *
     * @return oxuser
     */
    public function getUser()
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( $this->unitCustModUser ) ) {
                return $this->unitCustModUser;
            }
            $oUser = oxNew( 'oxuser' );
            if ( $oUser->loadActiveUser() ) {
                return $oUser;
            }
            return false;
        }

        if ( self::$_oActUser === null ) {
            self::$_oActUser = false;
            $oUser = oxNew( 'oxuser' );
            if ( $oUser->loadActiveUser() ) {
                self::$_oActUser = $oUser;
            }
        }

        return self::$_oActUser;
    }

    /**
     * Active oxuser object setter
     *
     * @param oxuser $oUser user object
     *
     * @return null
     */
    public function setUser( $oUser )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            $this->unitCustModUser = $oUser;
            return;
        }

        self::$_oActUser = $oUser;
    }

    /**
     * Admin mode status getter
     *
     * @return bool
     */
    public function isAdmin()
    {
        if ( self::$_blIsAdmin === null ) {
            self::$_blIsAdmin = isAdmin();
        }

        return self::$_blIsAdmin;
    }

    /**
     * Admin mode setter
     *
     * @param bool $blAdmin admin mode
     *
     * @return null
     */
    public function setAdminMode( $blAdmin )
    {
        self::$_blIsAdmin = $blAdmin;
    }

}
