<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

/**
 * Themes handler class.
 *
 * @internal Do not make a module extension for this class.
 */
class Theme extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Theme info array
     *
     * @var array
     */
    protected $_aTheme = [];

    /**
     * Theme info list
     *
     * @var array
     */
    protected $_aThemeList = [];

    /**
     * Load theme info
     *
     * @param string $sOXID theme id
     *
     * @return bool
     */
    public function load($sOXID)
    {
        $sFilePath = \OxidEsales\Eshop\Core\Registry::getConfig()->getViewsDir() . $sOXID . "/theme.php";
        if (file_exists($sFilePath) && is_readable($sFilePath)) {
            $aTheme = [];
            include $sFilePath;
            $this->_aTheme = $aTheme;
            $this->_aTheme['id'] = $sOXID;
            $this->_aTheme['active'] = (ContainerFacade::get(ThemeStateServiceInterface::class)->getActiveThemeId(
                ContainerFacade::get(ContextInterface::class)->getCurrentShopId()
            ) == $sOXID);

            return true;
        }

        return false;
    }

    /**
     * Load theme info list
     *
     * @return array
     */
    public function getList()
    {
        $this->_aThemeList = [];
        $sOutDir = \OxidEsales\Eshop\Core\Registry::getConfig()->getViewsDir();
        foreach (glob($sOutDir . "*", GLOB_ONLYDIR) as $sDir) {
            $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
            if ($oTheme->load(basename($sDir))) {
                $this->_aThemeList[$sDir] = $oTheme;
            }
        }

        return $this->_aThemeList;
    }

    /**
     * Return theme information
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
     * Return loaded parent
     *
     * @return \OxidEsales\Eshop\Core\Theme
     */
    public function getParent()
    {
        $sParent = $this->getInfo('parentTheme');
        if (!$sParent) {
            return null;
        }
        $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
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
        return $this->getInfo("id");
    }
}
