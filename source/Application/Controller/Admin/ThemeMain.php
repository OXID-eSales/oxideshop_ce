<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Theme;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ThemeMain extends AdminDetailsController
{
    private ThemeActivationServiceInterface $themeActivationService;
    private ThemeStateServiceInterface $themeStateService;
    private ContextInterface $context;

    public function __construct()
    {
        $this->themeActivationService = ContainerFacade::get(ThemeActivationServiceInterface::class);
        $this->themeStateService = ContainerFacade::get(ThemeStateServiceInterface::class);
        $this->context = ContainerFacade::get(ContextInterface::class);

        parent::__construct();
    }

    /** @inheritdoc */
    public function render()
    {
        $themeId = $this->resolveThemeId();

        if ($themeId !== null) {
            $theme = oxNew(Theme::class);
            if ($theme->load($themeId)) {
                $this->_aViewData['oTheme'] = $theme;
            } else {
                Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
            }
        }

        parent::render();

        return 'theme_main';
    }

    private function resolveThemeId(): ?string
    {
        $themeId = $this->getEditObjectId();
        if ($themeId) {
            return $themeId;
        }

        try {
            return $this->themeStateService->getActiveThemeId($this->context->getCurrentShopId());
        } catch (ActiveThemeNotFoundException) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');

            return null;
        }
    }

    /**
     * Activate the selected theme
     */
    public function setTheme()
    {
        $theme = oxNew(Theme::class);
        if (!$theme->load($this->getEditObjectId())) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');

            return;
        }

        $activationError = $theme->checkForActivationErrors();
        if ($activationError) {
            Registry::getUtilsView()->addErrorToDisplay($activationError);

            return;
        }

        try {
            $this->themeActivationService->activate($theme->getId(), $this->context->getCurrentShopId());
            $this->resetContentCache();
        } catch (ThemeConfigurationNotFoundException $exception) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
    }
}
