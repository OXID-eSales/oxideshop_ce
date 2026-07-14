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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\ParentInfo\ThemeParentInfoProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ThemeMain extends AdminDetailsController
{
    public function __construct(
        private readonly ThemeActivationServiceInterface $themeActivationService,
        private readonly ThemeStateServiceInterface $themeStateService,
        private readonly ThemeParentInfoProviderInterface $themeParentInfoProvider,
        private readonly ContextInterface $context,
    ) {
        parent::__construct();
    }

    /** @inheritdoc */
    public function render()
    {
        try {
            $this->addThemeViewData($this->resolveThemeId());
        } catch (ActiveThemeNotFoundException) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
        }

        parent::render();

        return 'theme_main';
    }

    private function addThemeViewData(string $themeId): void
    {
        $theme = oxNew(Theme::class);
        if ($theme->load($themeId)) {
            $this->_aViewData['oTheme'] = $theme;
            $this->addParentThemeViewData($themeId);
        } else {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
        }
    }

    private function addParentThemeViewData(string $themeId): void
    {
        $parentInfo = $this->themeParentInfoProvider->getByTheme($themeId, $this->context->getCurrentShopId());

        if ($parentInfo->hasResolutionError()) {
            $this->_aViewData['themeActivationError'] = 'EXCEPTION_THEME_INHERITANCE_INVALID';

            return;
        }

        if (!$parentInfo->exists()) {
            return;
        }

        $this->_aViewData['parentThemeId'] = $parentInfo->getId();
        $this->_aViewData['parentThemeTitle'] = $parentInfo->getTitle();
        $this->_aViewData['parentThemeVersions'] = $parentInfo->getCompatibleVersions();

        if ($parentInfo->hasActivationError()) {
            $this->_aViewData['themeActivationError'] = 'EXCEPTION_THEME_INHERITANCE_INVALID';
        }
    }

    /** @throws ActiveThemeNotFoundException */
    private function resolveThemeId(): string
    {
        $themeId = $this->getEditObjectId();

        return $themeId ?: $this->themeStateService->getActiveThemeId($this->context->getCurrentShopId());
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

        $shopId = $this->context->getCurrentShopId();

        try {
            $this->themeActivationService->activate($theme->getId(), $shopId);
            $this->resetContentCache();
        } catch (ThemeConfigurationNotFoundException $exception) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        } catch (InvalidThemeConfigurationException $exception) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_INHERITANCE_INVALID');
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        } catch (ThemeInheritanceException $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_INHERITANCE_INVALID');
        }
    }
}
