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
 * Class handling shop themes
 *
 */
class oxTheme extends oxSuperCfg
{
    /**
     * Theme info array
     * @var array
     */
    protected $_aTheme = array();

    /**
     * Theme info list
     * @var array
     */
    protected $_aThemeList = array();

    /**
     * Load theme info
     *
     * @param string $sOXID theme id
     *
     * @return bool
     */
    public function load($sOXID)
    {
        $sFilePath = $this->getConfig()->getViewsDir().$sOXID."/theme.php";
        if ( file_exists( $sFilePath ) && is_readable( $sFilePath ) ) {
            $aTheme = array();
            include $sFilePath;
            $this->_aTheme = $aTheme;
            $this->_aTheme['id'] = $sOXID;
            $this->_aTheme['active'] = ($this->getActiveThemeId() == $sOXID);
            return true;
        }

        return false;
    }

    /**
     * Set theme as active
     *
     * @return null
     */
    public function activate()
    {
        $sError = $this->checkForActivationErrors();
        if ($sError) {
            throw oxNew( "oxException", $sError );
        }
        $sParent = $this->getInfo('parentTheme');
        if ($sParent) {
            $this->getConfig()->saveShopConfVar("str", 'sTheme', $sParent);
            $this->getConfig()->saveShopConfVar("str", 'sCustomTheme', $this->getId());
        } else {
            $this->getConfig()->saveShopConfVar("str", 'sTheme', $this->getId());
            $this->getConfig()->saveShopConfVar("str", 'sCustomTheme', '');
        }
    }

    /**
     * Load theme info list
     *
     * @return array
     */
    public function getList()
    {
        $this->_aThemeList   = array();
        $sOutDir = $this->getConfig()->getViewsDir();
        foreach ( glob( $sOutDir."*", GLOB_ONLYDIR ) as $sDir ) {
            $oTheme = oxNew('oxTheme');
            if ($oTheme->load(basename($sDir))) {
                $this->_aThemeList[$sDir] = $oTheme;
            }
        }
        return $this->_aThemeList;
    }

    /**
     * get theme info item
     *
     * @param string $sName name of info item to retrieve
     *
     * @return mixed
     */
    public function getInfo($sName)
    {
        if (!isset($this->_aTheme[$sName])) {
            return null;
        }
        return $this->_aTheme[$sName];
    }

    /**
     * return current active theme, or custom theme if specified
     *
     * @return string
     */
    public function getActiveThemeId()
    {
        $sCustTheme = $this->getConfig()->getConfigParam('sCustomTheme');
        if ($sCustTheme) {
            return $sCustTheme;
        }
        return $this->getConfig()->getConfigParam('sTheme');
    }

    /**
     * return loaded parent
     *
     * @return oxTheme
     */
    public function getParent()
    {
        $sParent = $this->getInfo('parentTheme');
        if (!$sParent) {
            return null;
        }
        $oTheme = oxNew('oxTheme');
        if ($oTheme->load($sParent)) {
            return $oTheme;
        }
        return null;
    }

    /**
     * run pre-activation checks and return EXCEPTION_* translation string if error
     * found or false on success
     *
     * @return string
     */
    public function checkForActivationErrors()
    {
        if (!$this->getId()) {
            return 'EXCEPTION_THEME_NOT_LOADED';
        }
        $oParent = $this->getParent();
        if ($oParent) {
            $sParentVersion = $oParent->getInfo('version');
            if (!$sParentVersion) {
                return 'EXCEPTION_PARENT_VERSION_UNSPECIFIED';
            }
            $aMyParentVersions = $this->getInfo('parentVersions');
            if (!$aMyParentVersions || !is_array($aMyParentVersions)) {
                return 'EXCEPTION_UNSPECIFIED_PARENT_VERSIONS';
            }
            if (!in_array($sParentVersion, $aMyParentVersions)) {
                return 'EXCEPTION_PARENT_VERSION_MISMATCH';
            }
        } elseif ($this->getInfo('parentTheme')) {
            return 'EXCEPTION_PARENT_THEME_NOT_FOUND';
        }
        return false;
    }

    /**
     * Get theme ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getInfo( "id" );
    }
}

