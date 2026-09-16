<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\View\ThemeViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ThemeMain extends AdminDetailsController
{
    private ThemeActivationServiceInterface $themeActivationService;
    private ActiveThemeProviderInterface $activeThemeProvider;
    private ThemeViewServiceInterface $themeViewService;
    private ContextInterface $context;

    public function __construct()
    {
        $this->themeActivationService = ContainerFacade::get(ThemeActivationServiceInterface::class);
        $this->activeThemeProvider = ContainerFacade::get(ActiveThemeProviderInterface::class);
        $this->themeViewService = ContainerFacade::get(ThemeViewServiceInterface::class);
        $this->context = ContainerFacade::get(ContextInterface::class);

        parent::__construct();
    }

    public function render(): string
    {
        try {
            $shopId = $this->context->getCurrentShopId();
            $themeId = $this->getEditObjectId() ?: $this->activeThemeProvider->getActiveThemeId($shopId);

            $theme = $this->themeViewService->getTheme($themeId, $shopId);
            $this->_aViewData['theme'] = $theme;

            if ($theme->hasParentTheme()) {
                $this->_aViewData['parentTheme'] = $this->themeViewService->getParentTheme($themeId, $shopId);
            }
        } catch (ActiveThemeNotFoundException | ThemeNotLoadableException) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
        }

        parent::render();

        return 'theme_main';
    }

    public function setTheme(): void
    {
        $themeId = $this->getEditObjectId();

        try {
            $this->themeActivationService->activate($themeId, $this->context->getCurrentShopId());
        } catch (ThemeNotLoadableException $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
        } catch (ThemeParentCompatibilityException $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_INHERITANCE_INVALID');
        }
    }
}
