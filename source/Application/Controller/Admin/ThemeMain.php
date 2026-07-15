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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionUnspecifiedException;
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
    private ThemeParentProviderInterface $themeParentProvider;
    private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider;
    private ContextInterface $context;

    public function __construct()
    {
        $this->themeActivationService = ContainerFacade::get(ThemeActivationServiceInterface::class);
        $this->themeStateService = ContainerFacade::get(ThemeStateServiceInterface::class);
        $this->themeParentCompatibilityChecker = ContainerFacade::get(ThemeParentCompatibilityCheckerInterface::class);
        $this->themeParentProvider = ContainerFacade::get(ThemeParentProviderInterface::class);
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

        if ($this->themeParentProvider->hasParentTheme($themeId, $shopId)) {
            $parentThemeId = $this->themeParentProvider->getParentThemeId($themeId, $shopId);
            $this->_aViewData['parentThemeId'] = $parentThemeId;
            $this->_aViewData['parentThemeVersions'] = $this->themeMetaDataByIdProvider
                ->get($themeId, $shopId)
                ->getParentVersions();

            try {
                $this->_aViewData['parentThemeTitle'] = $this->themeMetaDataByIdProvider
                    ->get($parentThemeId, $shopId)
                    ->getTitle();
            } catch (ThemeConfigurationNotFoundException | \InvalidArgumentException) {
            }
        }

        if (!$this->themeStateService->isActive($themeId, $shopId)) {
            try {
                $this->themeParentCompatibilityChecker->assertCompatible($themeId, $shopId);
            } catch (
                ParentThemeNotInstalledException
                | ParentVersionUnspecifiedException
                | ParentVersionsNotDeclaredException
                | ParentVersionMismatchException $exception
            ) {
                $this->_aViewData['themeActivationError'] = $this->getParentCompatibilityErrorTranslationKey($exception);
            }
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
        } catch (
            ParentThemeNotInstalledException
            | ParentVersionUnspecifiedException
            | ParentVersionsNotDeclaredException
            | ParentVersionMismatchException $exception
        ) {
            Registry::getUtilsView()->addErrorToDisplay($this->getParentCompatibilityErrorTranslationKey($exception));

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

    private function getParentCompatibilityErrorTranslationKey(
        ParentThemeNotInstalledException|ParentVersionUnspecifiedException|ParentVersionsNotDeclaredException|ParentVersionMismatchException $exception
    ): string {
        return match (true) {
            $exception instanceof ParentThemeNotInstalledException => 'EXCEPTION_PARENT_THEME_NOT_FOUND',
            $exception instanceof ParentVersionUnspecifiedException => 'EXCEPTION_PARENT_VERSION_UNSPECIFIED',
            $exception instanceof ParentVersionsNotDeclaredException => 'EXCEPTION_UNSPECIFIED_PARENT_VERSIONS',
            $exception instanceof ParentVersionMismatchException => 'EXCEPTION_PARENT_VERSION_MISMATCH',
        };
    }
}
