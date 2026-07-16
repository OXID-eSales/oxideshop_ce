<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
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
        $shopId = ContainerFacade::get(ContextInterface::class)->getCurrentShopId();

        try {
            $themeMetaData = ContainerFacade::get(ThemeMetaDataByIdProviderInterface::class)->get($sOXID, $shopId);
        } catch (ThemeConfigurationNotFoundException | \InvalidArgumentException) {
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
     * Get theme ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getInfo("id");
    }
}
