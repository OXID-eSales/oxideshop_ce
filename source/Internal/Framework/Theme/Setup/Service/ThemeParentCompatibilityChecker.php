<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeMetadataInvalidException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionUnspecifiedException;

readonly class ThemeParentCompatibilityChecker implements ThemeParentCompatibilityCheckerInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
        private ThemeChainResolverInterface $themeChainResolver,
    ) {
    }

    public function assertCompatible(string $themeId, int $shopId): void
    {
        $chain = $this->themeChainResolver->getThemeChain($themeId, $shopId);

        if (!$chain->hasParentTheme()) {
            return;
        }

        $parentThemeId = $chain->getParentThemeId();

        $this->assertParentThemeIsInstalled($parentThemeId, $shopId);

        $parentVersion = $this->resolveParentVersion($parentThemeId, $shopId);
        $declaredParentVersions = $this->resolveDeclaredParentVersions($themeId, $shopId);

        $this->assertVersionIsCompatible($parentVersion, $declaredParentVersions);
    }

    private function assertParentThemeIsInstalled(string $parentThemeId, int $shopId): void
    {
        if (!$this->themeConfigurationDao->exists($parentThemeId, $shopId)) {
            throw new ParentThemeNotInstalledException();
        }
    }

    private function resolveParentVersion(string $parentThemeId, int $shopId): string
    {
        $parentVersion = $this->getThemeMetaData($parentThemeId, $shopId)->getVersion();
        if ($parentVersion === '') {
            throw new ParentVersionUnspecifiedException();
        }

        return $parentVersion;
    }

    private function resolveDeclaredParentVersions(string $themeId, int $shopId): array
    {
        $declaredParentVersions = $this->getThemeMetaData($themeId, $shopId)->getParentVersions();
        if (empty($declaredParentVersions)) {
            throw new ParentVersionsNotDeclaredException();
        }

        return $declaredParentVersions;
    }

    private function assertVersionIsCompatible(string $parentVersion, array $declaredParentVersions): void
    {
        if (!in_array($parentVersion, $declaredParentVersions, true)) {
            throw new ParentVersionMismatchException();
        }
    }

    private function getThemeMetaData(string $themeId, int $shopId): ThemeMetaData
    {
        try {
            return $this->themeMetaDataByIdProvider->get($themeId, $shopId);
        } catch (\InvalidArgumentException $exception) {
            throw new ParentThemeMetadataInvalidException(previous: $exception);
        }
    }
}