<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\ParentInfo;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeInheritanceException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use Psr\Log\LoggerInterface;

readonly class ThemeParentInfoProvider implements ThemeParentInfoProviderInterface
{
    public function __construct(
        private ThemeInheritanceResolverInterface $themeInheritanceResolver,
        private ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider,
        private ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker,
        private ThemeStateServiceInterface $themeStateService,
        private LoggerInterface $logger,
    ) {
    }

    public function getParentInfo(string $themeId, int $shopId): ThemeParentInfo
    {
        try {
            $inheritance = $this->themeInheritanceResolver->resolve($themeId, $shopId);
        } catch (ThemeConfigurationNotFoundException | InvalidThemeMetaDataException | ThemeInheritanceException $exception) {
            $this->logger->warning($exception->getMessage(), [$exception]);

            return new ThemeParentInfo(new ThemeInheritance($themeId, null), null, [], false, hasResolutionError: true);
        }

        if (!$inheritance->hasParentTheme()) {
            return new ThemeParentInfo($inheritance, null, [], false);
        }

        $parentThemeId = $inheritance->getParentThemeId();
        [$parentThemeTitle, $parentThemeVersions] = $this->resolveDisplayData($themeId, $parentThemeId, $shopId);

        return new ThemeParentInfo(
            $inheritance,
            $parentThemeTitle,
            $parentThemeVersions,
            $this->hasActivationError($themeId, $parentThemeId, $shopId)
        );
    }

    /** @return array{0: ?string, 1: string[]} */
    private function resolveDisplayData(string $themeId, string $parentThemeId, int $shopId): array
    {
        try {
            return [
                $this->themeMetaDataByIdProvider->getById($parentThemeId, $shopId)->getTitle(),
                $this->themeMetaDataByIdProvider->getById($themeId, $shopId)->getParentVersions(),
            ];
        } catch (ThemeConfigurationNotFoundException | InvalidThemeMetaDataException $exception) {
            $this->logger->warning($exception->getMessage(), [$exception]);

            return [null, []];
        }
    }

    private function hasActivationError(string $themeId, string $parentThemeId, int $shopId): bool
    {
        if ($this->themeStateService->isActive($themeId, $shopId)) {
            return false;
        }

        try {
            $this->themeParentCompatibilityChecker->validateCompatibility($themeId, $parentThemeId, $shopId);

            return false;
        } catch (ThemeInheritanceException $exception) {
            $this->logger->error($exception->getMessage(), [$exception]);

            return true;
        }
    }
}
