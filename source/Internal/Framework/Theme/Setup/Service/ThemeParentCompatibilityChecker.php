<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionUnspecifiedException;

readonly class ThemeParentCompatibilityChecker implements ThemeParentCompatibilityCheckerInterface
{
    public function __construct(
        private ThemeParentProviderInterface $themeParentProvider,
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
    ) {
    }

    public function assertCompatible(string $themeId, int $shopId): void
    {
        if (!$this->themeParentProvider->hasParentTheme($themeId, $shopId)) {
            return;
        }

        $parentThemeId = $this->themeParentProvider->getParentThemeId($themeId, $shopId);

        if (!$this->themeConfigurationDao->exists($parentThemeId, $shopId)) {
            throw new ParentThemeNotInstalledException();
        }

        $parentVersion = $this->themeMetaDataByIdProvider->get($parentThemeId, $shopId)->getVersion();
        if ($parentVersion === '') {
            throw new ParentVersionUnspecifiedException();
        }

        $declaredParentVersions = $this->themeMetaDataByIdProvider->get($themeId, $shopId)->getParentVersions();
        if (empty($declaredParentVersions)) {
            throw new ParentVersionsNotDeclaredException();
        }

        if (!in_array($parentVersion, $declaredParentVersions, true)) {
            throw new ParentVersionMismatchException();
        }
    }
}
