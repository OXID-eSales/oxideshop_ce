<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ThemeParentCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ThemeParentDepthExceededException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service\ThemeParentCompatibilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use Psr\Log\LoggerInterface;

readonly class ThemeViewService implements ThemeViewServiceInterface
{
    public function __construct(
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
        private ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker,
        private string $activeThemeId,
        private LoggerInterface $logger,
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
    ) {
    }

    public function getTheme(string $themeId, int $shopId): ThemeView
    {
        $metaData = $this->themeMetaDataByIdProvider->getById($themeId, $shopId);

        return new ThemeView(
            $metaData->getId(),
            $metaData->getTitle(),
            $metaData->getDescription(),
            $metaData->getThumbnail(),
            $metaData->getAuthor(),
            $metaData->getVersion(),
            $metaData->getParentTheme(),
            $themeId === $this->activeThemeId,
        );
    }

    public function getThemes(int $shopId): array
    {
        $themes = [];
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeId => $configuration) {
            try {
                $themes[$themeId] = $this->getTheme($themeId, $shopId);
            } catch (ThemeNotLoadableException $exception) {
                $this->logger->error($exception->getMessage(), [$exception]);
            }
        }

        return $themes;
    }

    public function getParentTheme(string $themeId, int $shopId): ParentThemeView
    {
        $metaData = $this->themeMetaDataByIdProvider->getById($themeId, $shopId);

        return new ParentThemeView(
            $metaData->getParentTheme(),
            $this->resolveParentThemeTitle($metaData->getParentTheme(), $shopId),
            $metaData->getParentVersions(),
            $this->isCompatible($themeId, $shopId),
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
