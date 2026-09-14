<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentDepthExceededException;

readonly class ThemeParentCompatibilityChecker implements ThemeParentCompatibilityCheckerInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
    ) {
    }

    public function validate(string $themeId, int $shopId): void
    {
        $metaData = $this->themeMetaDataByIdProvider->getById($themeId, $shopId);
        $parentThemeId = $metaData->getParentTheme();

        if ($parentThemeId === '') {
            return;
        }

        if ($parentThemeId === $themeId) {
            throw new ThemeParentCycleException(
                "Theme '$themeId' declares itself as its own parent theme"
            );
        }

        if (!$this->themeConfigurationDao->exists($parentThemeId, $shopId)) {
            throw new ThemeParentCompatibilityException(
                "Theme '$themeId' declares parent theme '$parentThemeId', but '$parentThemeId' is not installed"
            );
        }

        $parentMetaData = $this->themeMetaDataByIdProvider->getById($parentThemeId, $shopId);

        if ($parentMetaData->getParentTheme() !== '') {
            throw new ThemeParentDepthExceededException(
                "Theme '$themeId' declares '$parentThemeId' as its parent, but '$parentThemeId' is itself "
                . 'a child theme; only one level of theme inheritance is supported'
            );
        }

        $this->validateVersionIsCompatible($metaData, $parentMetaData);
    }

    private function validateVersionIsCompatible(ThemeMetaData $metaData, ThemeMetaData $parentMetaData): void
    {
        if ($parentMetaData->getVersion() === '') {
            throw new ThemeParentCompatibilityException(
                "Parent theme '{$parentMetaData->getId()}' does not declare a version in its metadata.yaml"
            );
        }

        if ($metaData->getParentVersions() === []) {
            throw new ThemeParentCompatibilityException(
                "Theme '{$metaData->getId()}' does not declare any compatible parent versions in its metadata.yaml"
            );
        }

        if (!in_array($parentMetaData->getVersion(), $metaData->getParentVersions(), true)) {
            throw new ThemeParentCompatibilityException(
                "Theme '{$metaData->getId()}' declares compatible parent versions ["
                . implode(', ', $metaData->getParentVersions())
                . "], but installed parent theme '{$parentMetaData->getId()}' has version "
                . "'{$parentMetaData->getVersion()}'"
            );
        }
    }
}
