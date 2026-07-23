<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChain;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeMetadataInvalidException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentThemeNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionUnspecifiedException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityChecker;
use PHPUnit\Framework\TestCase;

final class ThemeParentCompatibilityCheckerTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'child';
    private const PARENT_THEME_ID = 'apex';

    public function testAssertCompatibleDoesNothingWhenThemeHasNoParent(): void
    {
        $checker = $this->createChecker(hasParent: false);

        $checker->assertCompatible(self::THEME_ID, self::SHOP_ID);

        $this->addToAssertionCount(1);
    }

    public function testAssertCompatibleThrowsWhenParentThemeIsNotInstalled(): void
    {
        $checker = $this->createChecker(hasParent: true, parentInstalled: false);

        $this->expectException(ParentThemeNotInstalledException::class);

        $checker->assertCompatible(self::THEME_ID, self::SHOP_ID);
    }

    public function testAssertCompatibleThrowsWhenParentVersionIsUnspecified(): void
    {
        $checker = $this->createChecker(hasParent: true, parentInstalled: true, parentVersion: '');

        $this->expectException(ParentVersionUnspecifiedException::class);

        $checker->assertCompatible(self::THEME_ID, self::SHOP_ID);
    }

    public function testAssertCompatibleThrowsWhenParentVersionsAreNotDeclared(): void
    {
        $checker = $this->createChecker(
            hasParent: true,
            parentInstalled: true,
            parentVersion: '1.0.0',
            declaredParentVersions: []
        );

        $this->expectException(ParentVersionsNotDeclaredException::class);

        $checker->assertCompatible(self::THEME_ID, self::SHOP_ID);
    }

    public function testAssertCompatibleThrowsWhenParentVersionDoesNotMatchDeclaredVersions(): void
    {
        $checker = $this->createChecker(
            hasParent: true,
            parentInstalled: true,
            parentVersion: '2.0.0',
            declaredParentVersions: ['1.0.0', '1.1.0']
        );

        $this->expectException(ParentVersionMismatchException::class);

        $checker->assertCompatible(self::THEME_ID, self::SHOP_ID);
    }

    public function testAssertCompatibleDoesNothingWhenParentVersionMatchesDeclaredVersions(): void
    {
        $checker = $this->createChecker(
            hasParent: true,
            parentInstalled: true,
            parentVersion: '1.1.0',
            declaredParentVersions: ['1.0.0', '1.1.0']
        );

        $checker->assertCompatible(self::THEME_ID, self::SHOP_ID);

        $this->addToAssertionCount(1);
    }

    public function testAssertCompatibleThrowsWhenParentThemeMetadataIsUnreadable(): void
    {
        $themeChainResolver = $this->createStub(ThemeChainResolverInterface::class);
        $themeChainResolver->method('getThemeChain')->willReturn(new ThemeChain([self::THEME_ID, self::PARENT_THEME_ID]));

        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('exists')->willReturn(true);

        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('get')->willThrowException(new \InvalidArgumentException());

        $checker = new ThemeParentCompatibilityChecker($themeConfigurationDao, $themeMetaDataByIdProvider, $themeChainResolver);

        $this->expectException(ParentThemeMetadataInvalidException::class);

        $checker->assertCompatible(self::THEME_ID, self::SHOP_ID);
    }

    private function createChecker(
        bool $hasParent,
        bool $parentInstalled = false,
        string $parentVersion = '',
        array $declaredParentVersions = []
    ): ThemeParentCompatibilityChecker {
        $themeChainResolver = $this->createStub(ThemeChainResolverInterface::class);
        $themeChainResolver->method('getThemeChain')->willReturn(
            new ThemeChain($hasParent ? [self::THEME_ID, self::PARENT_THEME_ID] : [self::THEME_ID])
        );

        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('exists')->willReturn($parentInstalled);

        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('get')->willReturnCallback(
            fn(string $themeId): ThemeMetaData => $themeId === self::PARENT_THEME_ID
                ? (new ThemeMetaData())->setId(self::PARENT_THEME_ID)->setVersion($parentVersion)
                : (new ThemeMetaData())->setId(self::THEME_ID)->setParentVersions($declaredParentVersions)
        );

        return new ThemeParentCompatibilityChecker($themeConfigurationDao, $themeMetaDataByIdProvider, $themeChainResolver);
    }
}
