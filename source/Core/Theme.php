<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;

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
        $shopId = Registry::getConfig()->getShopId();

        try {
            $themeMetaData = ContainerFacade::get(ThemeMetaDataByIdProviderInterface::class)->getById($sOXID, $shopId);
        } catch (ThemeConfigurationNotFoundException | InvalidThemeMetaDataException) {
            return false;
        }

        if ($themeMetaData->getId() !== $sOXID) {
            return false;
        }

        $this->_aTheme = [
            'id' => $themeMetaData->getId(),
            'title' => $themeMetaData->getTitle(),
            'description' => $themeMetaData->getDescription(),
            'thumbnail' => $themeMetaData->getThumbnail(),
            'version' => $themeMetaData->getVersion(),
            'author' => $themeMetaData->getAuthor(),
            'parentTheme' => $themeMetaData->getParentTheme(),
            'parentVersions' => $themeMetaData->getParentVersions(),
        ];

        try {
            $activeThemeId = ContainerFacade::get(ThemeStateServiceInterface::class)->getActiveThemeId($shopId);
            $this->_aTheme['active'] = ($activeThemeId === $sOXID);
        } catch (ActiveThemeNotFoundException) {
            $this->_aTheme['active'] = false;
        }

        return true;
    }

    /**
     * Load theme info list
     *
     * @return array
     */
    public function getList()
    {
        $this->_aThemeList = [];
        $shopId = Registry::getConfig()->getShopId();
        foreach (ContainerFacade::get(ThemeConfigurationDaoInterface::class)->getAll($shopId) as $themeConfiguration) {
            $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
            if ($oTheme->load($themeConfiguration->getId())) {
                $this->_aThemeList[$themeConfiguration->getId()] = $oTheme;
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
