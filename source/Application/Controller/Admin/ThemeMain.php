<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\ThemeViewItemFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Bridge\ThemeActivationBridgeInterface;

class ThemeMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        $shopId = (int) Registry::getConfig()->getShopId();
        $themeId = $this->getEditObjectId();

        if (!$themeId) {
            $themeId = ContainerFacade::get(ActiveThemeServiceInterface::class)->getActiveThemeId();
        }

        $theme = ContainerFacade::get(ThemeViewItemFactoryInterface::class)->get($themeId, $shopId);

        if ($theme !== null) {
            $this->_aViewData['oTheme'] = $theme;
        } else {
            Registry::getUtilsView()->addErrorToDisplay(
                oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, 'EXCEPTION_THEME_NOT_LOADED')
            );
        }

        parent::render();

        return 'theme_main';
    }

    /**
     * Activate the selected theme.
     */
    public function setTheme()
    {
        $themeId = $this->getEditObjectId();
        $shopId = (int) Registry::getConfig()->getShopId();

        try {
            ContainerFacade::get(ThemeActivationBridgeInterface::class)->activate($themeId, $shopId);
            $this->resetContentCache();
        } catch (\Throwable $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception);
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
    }
}
