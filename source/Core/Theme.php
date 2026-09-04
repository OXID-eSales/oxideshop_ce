<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;

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
        } catch (ThemeNotLoadableException) {
            return false;
        }

        $this->_aTheme = [
            'id' => $themeMetaData->getId(),
            'title' => $themeMetaData->getTitle(),
            'description' => $themeMetaData->getDescription(),
            'thumbnail' => $themeMetaData->getThumbnail(),
            'version' => $themeMetaData->getVersion(),
            'author' => $themeMetaData->getAuthor(),
        ];

        try {
            $activeThemeId = ContainerFacade::get(ActiveThemeProviderInterface::class)->getActiveThemeId($shopId);
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
     * Get theme ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getInfo("id");
    }
}
