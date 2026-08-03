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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeInheritanceException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ThemeMain extends AdminDetailsController
{
    private ThemeActivationServiceInterface $themeActivationService;
    private ThemeStateServiceInterface $themeStateService;
    private ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker;
    private ThemeInheritanceResolverInterface $themeInheritanceResolver;
    private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider;
    private ContextInterface $context;

    public function __construct()
    {
        $this->themeActivationService = ContainerFacade::get(ThemeActivationServiceInterface::class);
        $this->themeStateService = ContainerFacade::get(ThemeStateServiceInterface::class);
        $this->themeParentCompatibilityChecker = ContainerFacade::get(ThemeParentCompatibilityCheckerInterface::class);
        $this->themeInheritanceResolver = ContainerFacade::get(ThemeInheritanceResolverInterface::class);
        $this->themeMetaDataByIdProvider = ContainerFacade::get(ThemeMetaDataByIdProviderInterface::class);
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
                $this->addParentThemeViewData($themeId);
            } else {
                Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
            }
        }

        parent::render();

        return 'theme_main';
    }

    private function addParentThemeViewData(string $themeId): void
    {
        $shopId = $this->context->getCurrentShopId();

        $this->addParentThemeDetailsViewData($themeId, $shopId);
        $this->addThemeActivationErrorViewData($themeId, $shopId);
    }

    private function addParentThemeDetailsViewData(string $themeId, int $shopId): void
    {
        try {
            $inheritance = $this->themeInheritanceResolver->resolve($themeId, $shopId);
        } catch (ThemeConfigurationNotFoundException | InvalidThemeMetaDataException | ThemeInheritanceException $exception) {
            Registry::getLogger()->warning($exception->getMessage(), [$exception]);

            return;
        }

        if (!$inheritance->hasParentTheme()) {
            return;
        }

        $parentThemeId = $inheritance->getParentThemeId();
        $this->_aViewData['parentThemeId'] = $parentThemeId;

        try {
            $this->_aViewData['parentThemeVersions'] = $this->themeMetaDataByIdProvider
                ->get($themeId, $shopId)
                ->getParentVersions();
            $this->_aViewData['parentThemeTitle'] = $this->themeMetaDataByIdProvider
                ->get($parentThemeId, $shopId)
                ->getTitle();
        } catch (ThemeConfigurationNotFoundException | InvalidThemeMetaDataException $exception) {
            Registry::getLogger()->warning($exception->getMessage(), [$exception]);
        }
    }

    private function addThemeActivationErrorViewData(string $themeId, int $shopId): void
    {
        if ($this->themeStateService->isActive($themeId, $shopId)) {
            return;
        }

        try {
            $this->themeParentCompatibilityChecker->assertCompatible($themeId, $shopId);
        } catch (ThemeInheritanceException $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            $this->_aViewData['themeActivationError'] = 'EXCEPTION_THEME_INHERITANCE_INVALID';
        }
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

        $shopId = $this->context->getCurrentShopId();

        try {
            $this->themeParentCompatibilityChecker->assertCompatible($theme->getId(), $shopId);
        } catch (ThemeInheritanceException $exception) {
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_INHERITANCE_INVALID');

            return;
        }

        try {
            $this->themeActivationService->activate($theme->getId(), $shopId);
            $this->resetContentCache();
        } catch (ThemeConfigurationNotFoundException $exception) {
            Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_THEME_NOT_LOADED');
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
    }
}
