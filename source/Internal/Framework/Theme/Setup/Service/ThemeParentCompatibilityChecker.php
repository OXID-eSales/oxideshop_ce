<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentVersionUnspecifiedException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;

readonly class ThemeParentCompatibilityChecker implements ThemeParentCompatibilityCheckerInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
    ) {
    }

    public function validate(string $themeId, string $parentThemeId, int $shopId): void
    {
        try {
            $this->validateParentThemeIsInstalled($themeId, $parentThemeId, $shopId);

            $parentVersion = $this->resolveParentVersion($parentThemeId, $shopId);
            $declaredParentVersions = $this->resolveDeclaredParentVersions($themeId, $shopId);

            $this->validateVersionIsCompatible($themeId, $parentThemeId, $parentVersion, $declaredParentVersions);
        } catch (InvalidThemeMetaDataException $exception) {
            throw new ThemeInheritanceMetaDataException(
                "Could not read metadata of theme '$themeId' or its parent theme: {$exception->getMessage()}",
                previous: $exception
            );
        }
    }

    private function validateParentThemeIsInstalled(string $themeId, string $parentThemeId, int $shopId): void
    {
        if (!$this->themeConfigurationDao->exists($parentThemeId, $shopId)) {
            throw new ThemeParentNotInstalledException(
                "Theme '$themeId' declares parent theme '$parentThemeId', but '$parentThemeId' is not installed"
            );
        }
    }

    private function resolveParentVersion(string $parentThemeId, int $shopId): string
    {
        $parentVersion = $this->themeMetaDataByIdProvider->getById($parentThemeId, $shopId)->getVersion();
        if ($parentVersion === '') {
            throw new ThemeParentVersionUnspecifiedException(
                "Parent theme '$parentThemeId' does not declare a version in its metadata.yaml"
            );
        }

        return $parentVersion;
    }

    private function resolveDeclaredParentVersions(string $themeId, int $shopId): array
    {
        $declaredParentVersions = $this->themeMetaDataByIdProvider->getById($themeId, $shopId)->getParentVersions();
        if (empty($declaredParentVersions)) {
            throw new ThemeParentVersionsNotDeclaredException(
                "Theme '$themeId' does not declare any compatible parent versions in its metadata.yaml"
            );
        }

        return $declaredParentVersions;
    }

    private function validateVersionIsCompatible(
        string $themeId,
        string $parentThemeId,
        string $parentVersion,
        array $declaredParentVersions
    ): void {
        if (!in_array($parentVersion, $declaredParentVersions, true)) {
            throw new ThemeParentVersionMismatchException(
                "Theme '$themeId' declares compatible parent versions [" . implode(', ', $declaredParentVersions)
                . "], but installed parent theme '$parentThemeId' has version '$parentVersion'"
            );
        }
    }
}
