<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentDepthExceededException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use Psr\Log\LoggerInterface;

readonly class ThemeViewService implements ThemeViewServiceInterface
{
    public function __construct(
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
        private ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker,
        private ActiveThemeProviderInterface $activeThemeProvider,
        private LoggerInterface $logger,
    ) {
    }

    public function getTheme(string $themeId, int $shopId): ThemeView
    {
        return new ThemeView(
            $this->themeMetaDataByIdProvider->getById($themeId, $shopId),
            $this->activeThemeProvider->isActive($themeId, $shopId),
        );
    }

    public function getParentTheme(string $themeId, int $shopId): ParentThemeView
    {
        $metaData = $this->themeMetaDataByIdProvider->getById($themeId, $shopId);

        return new ParentThemeView(
            $metaData->getParentTheme(),
            $this->resolveParentThemeTitle($metaData->getParentTheme(), $shopId),
            $metaData->getParentVersions(),
            $this->activeThemeProvider->isActive($themeId, $shopId) || $this->isCompatible($themeId, $shopId),
        );
    }

    private function resolveParentThemeTitle(string $parentThemeId, int $shopId): string
    {
        try {
            return $this->themeMetaDataByIdProvider->getById($parentThemeId, $shopId)->getTitle();
        } catch (ThemeNotLoadableException) {
            return '';
        }
    }

    private function isCompatible(string $themeId, int $shopId): bool
    {
        try {
            $this->themeParentCompatibilityChecker->validate($themeId, $shopId);
        } catch (ThemeParentCycleException | ThemeParentDepthExceededException $exception) {
            $this->logger->warning($exception->getMessage(), [$exception]);

            return false;
        } catch (ThemeParentCompatibilityException | InvalidThemeMetaDataException $exception) {
            $this->logger->error($exception->getMessage(), [$exception]);

            return false;
        }

        return true;
    }
}
