<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxTheme;
use oxException;

/**
 * Admin article main deliveryset manager.
 * There is possibility to change deliveryset name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main Sets.
 */
class ThemeMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $soxId = $this->getEditObjectId();

        $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);

        if (!$soxId) {
            $soxId = $oTheme->getActiveThemeId();
        }

        if ($oTheme->load($soxId)) {
            $this->_aViewData["oTheme"] = $oTheme;
        } else {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_THEME_NOT_LOADED'));
        }

        parent::render();

        if ($this->themeInConfigFile()) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_SHOULD_BE_ONLY_IN_DATABASE');
        }

        return 'theme_main.tpl';
    }

    /**
     * Check if theme config is in config file.
     *
     * @return bool
     */
    public function themeInConfigFile()
    {
        $blThemeSet = isset($this->getConfig()->sTheme);
        $blCustomThemeSet = isset($this->getConfig()->sCustomTheme);

        return ($blThemeSet || $blCustomThemeSet);
    }


    /**
     * Set theme
     *
     * @return null
     */
    public function setTheme()
    {
        $sTheme = $this->getEditObjectId();
        /** @var \OxidEsales\Eshop\Core\Theme $oTheme */
        $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
        if (!$oTheme->load($sTheme)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_THEME_NOT_LOADED'));

            return;
        }
        try {
            $oTheme->activate();
            $this->resetContentCache();
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $oEx) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx);
            $oEx->debugOut();
        }
    }
}
