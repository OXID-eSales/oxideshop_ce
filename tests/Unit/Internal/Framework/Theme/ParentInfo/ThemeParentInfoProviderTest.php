<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\ParentInfo;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\ParentInfo\ThemeParentInfoProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ThemeParentInfoProviderTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'child';
    private const PARENT_THEME_ID = 'apex';

    public function testGetParentInfoReturnsEmptyInfoWhenThemeHasNoParent(): void
    {
        $provider = $this->createProvider(
            themeInheritanceResolver: $this->createResolverStub(hasParent: false)
        );

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertFalse($parentInfo->hasParentTheme());
        $this->assertFalse($parentInfo->hasActivationError());
        $this->assertFalse($parentInfo->hasResolutionError());
    }

    public function testGetParentInfoReturnsEmptyInfoAndLogsWarningWhenResolvingInheritanceFails(): void
    {
        $themeInheritanceResolver = $this->createStub(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->method('resolve')->willThrowException(new InvalidThemeMetaDataException());

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $provider = $this->createProvider(themeInheritanceResolver: $themeInheritanceResolver, logger: $logger);

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertFalse($parentInfo->hasParentTheme());
        $this->assertTrue($parentInfo->hasResolutionError());
    }

    public function testGetParentInfoReturnsParentDisplayDataWhenThemeHasParent(): void
    {
        $provider = $this->createProvider(
            themeInheritanceResolver: $this->createResolverStub(hasParent: true),
            themeMetaDataByIdProvider: $this->createMetaDataStub(parentTitle: 'Apex', declaredParentVersions: ['1.0.0']),
            themeStateService: $this->createStateServiceStub(isActive: true)
        );

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertTrue($parentInfo->hasParentTheme());
        $this->assertSame(self::PARENT_THEME_ID, $parentInfo->getParentThemeId());
        $this->assertSame('Apex', $parentInfo->getParentThemeTitle());
        $this->assertSame(['1.0.0'], $parentInfo->getParentThemeVersions());
    }

    public function testGetParentInfoReturnsFalsyButValidParentThemeTitle(): void
    {
        $provider = $this->createProvider(
            themeInheritanceResolver: $this->createResolverStub(hasParent: true),
            themeMetaDataByIdProvider: $this->createMetaDataStub(parentTitle: '0'),
            themeStateService: $this->createStateServiceStub(isActive: true)
        );

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertSame('0', $parentInfo->getParentThemeTitle());
    }

    public function testGetParentInfoReturnsEmptyDisplayDataAndLogsWarningWhenParentMetadataIsUnreadable(): void
    {
        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('getById')->willThrowException(new InvalidThemeMetaDataException());

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $provider = $this->createProvider(
            themeInheritanceResolver: $this->createResolverStub(hasParent: true),
            themeMetaDataByIdProvider: $themeMetaDataByIdProvider,
            themeStateService: $this->createStateServiceStub(isActive: true),
            logger: $logger
        );

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertNull($parentInfo->getParentThemeTitle());
        $this->assertSame([], $parentInfo->getParentThemeVersions());
    }

    public function testGetParentInfoHasNoActivationErrorWhenThemeIsAlreadyActive(): void
    {
        $themeParentCompatibilityChecker = $this->createMock(ThemeParentCompatibilityCheckerInterface::class);
        $themeParentCompatibilityChecker->expects($this->never())->method('validateCompatibility');

        $provider = $this->createProvider(
            themeInheritanceResolver: $this->createResolverStub(hasParent: true),
            themeMetaDataByIdProvider: $this->createMetaDataStub(),
            themeParentCompatibilityChecker: $themeParentCompatibilityChecker,
            themeStateService: $this->createStateServiceStub(isActive: true)
        );

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertFalse($parentInfo->hasActivationError());
    }

    public function testGetParentInfoHasNoActivationErrorWhenInactiveThemeIsCompatible(): void
    {
        $themeParentCompatibilityChecker = $this->createStub(ThemeParentCompatibilityCheckerInterface::class);

        $provider = $this->createProvider(
            themeInheritanceResolver: $this->createResolverStub(hasParent: true),
            themeMetaDataByIdProvider: $this->createMetaDataStub(),
            themeParentCompatibilityChecker: $themeParentCompatibilityChecker,
            themeStateService: $this->createStateServiceStub(isActive: false)
        );

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertFalse($parentInfo->hasActivationError());
    }

    public function testGetParentInfoHasActivationErrorAndLogsWhenInactiveThemeIsIncompatible(): void
    {
        $themeParentCompatibilityChecker = $this->createStub(ThemeParentCompatibilityCheckerInterface::class);
        $themeParentCompatibilityChecker->method('validateCompatibility')->willThrowException(new ThemeParentVersionMismatchException());

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $provider = $this->createProvider(
            themeInheritanceResolver: $this->createResolverStub(hasParent: true),
            themeMetaDataByIdProvider: $this->createMetaDataStub(),
            themeParentCompatibilityChecker: $themeParentCompatibilityChecker,
            themeStateService: $this->createStateServiceStub(isActive: false),
            logger: $logger
        );

        $parentInfo = $provider->getParentInfo(self::THEME_ID, self::SHOP_ID);

        $this->assertTrue($parentInfo->hasActivationError());
    }

    private function createResolverStub(bool $hasParent): ThemeInheritanceResolverInterface
    {
        $themeInheritanceResolver = $this->createStub(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->method('resolve')->willReturn(
            new ThemeInheritance(self::THEME_ID, $hasParent ? self::PARENT_THEME_ID : null)
        );

        return $themeInheritanceResolver;
    }

    private function createMetaDataStub(
        string $parentTitle = '',
        array $declaredParentVersions = []
    ): ThemeMetaDataByIdProviderInterface {
        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('getById')->willReturnCallback(
            fn(string $themeId): ThemeMetaData => $themeId === self::PARENT_THEME_ID
                ? (new ThemeMetaData())->setId(self::PARENT_THEME_ID)->setTitle($parentTitle)
                : (new ThemeMetaData())->setId(self::THEME_ID)->setParentVersions($declaredParentVersions)
        );

        return $themeMetaDataByIdProvider;
    }

    private function createStateServiceStub(bool $isActive): ThemeStateServiceInterface
    {
        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        $themeStateService->method('isActive')->willReturn($isActive);

        return $themeStateService;
    }

    private function createProvider(
        ?ThemeInheritanceResolverInterface $themeInheritanceResolver = null,
        ?ThemeMetaDataByIdProviderInterface $themeMetaDataByIdProvider = null,
        ?ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker = null,
        ?ThemeStateServiceInterface $themeStateService = null,
        ?LoggerInterface $logger = null,
    ): ThemeParentInfoProvider {
        return new ThemeParentInfoProvider(
            $themeInheritanceResolver ?? $this->createResolverStub(hasParent: false),
            $themeMetaDataByIdProvider ?? $this->createStub(ThemeMetaDataByIdProviderInterface::class),
            $themeParentCompatibilityChecker ?? $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $themeStateService ?? $this->createStateServiceStub(isActive: true),
            $logger ?? $this->createStub(LoggerInterface::class),
        );
    }
}
