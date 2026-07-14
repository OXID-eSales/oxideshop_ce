<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentVersionsNotDeclaredException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentVersionUnspecifiedException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityChecker;
use PHPUnit\Framework\TestCase;

final class ThemeParentCompatibilityCheckerTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'child';
    private const PARENT_THEME_ID = 'apex';

    public function testValidateThrowsWhenParentThemeIsNotInstalled(): void
    {
        $checker = $this->createChecker(parentInstalled: false);

        $this->expectException(ThemeParentNotInstalledException::class);

        $checker->validate(self::THEME_ID, self::PARENT_THEME_ID, self::SHOP_ID);
    }

    public function testValidateThrowsWhenParentVersionIsUnspecified(): void
    {
        $checker = $this->createChecker(parentInstalled: true, parentVersion: '');

        $this->expectException(ThemeParentVersionUnspecifiedException::class);

        $checker->validate(self::THEME_ID, self::PARENT_THEME_ID, self::SHOP_ID);
    }

    public function testValidateThrowsWhenParentVersionsAreNotDeclared(): void
    {
        $checker = $this->createChecker(
            parentInstalled: true,
            parentVersion: '1.0.0',
            declaredParentVersions: []
        );

        $this->expectException(ThemeParentVersionsNotDeclaredException::class);

        $checker->validate(self::THEME_ID, self::PARENT_THEME_ID, self::SHOP_ID);
    }

    public function testValidateThrowsWhenParentVersionDoesNotMatchDeclaredVersions(): void
    {
        $checker = $this->createChecker(
            parentInstalled: true,
            parentVersion: '2.0.0',
            declaredParentVersions: ['1.0.0', '1.1.0']
        );

        $this->expectException(ThemeParentVersionMismatchException::class);

        $checker->validate(self::THEME_ID, self::PARENT_THEME_ID, self::SHOP_ID);
    }

    public function testValidateDoesNothingWhenParentVersionMatchesDeclaredVersions(): void
    {
        $checker = $this->createChecker(
            parentInstalled: true,
            parentVersion: '1.1.0',
            declaredParentVersions: ['1.0.0', '1.1.0']
        );

        $checker->validate(self::THEME_ID, self::PARENT_THEME_ID, self::SHOP_ID);

        $this->addToAssertionCount(1);
    }

    public function testValidateThrowsWhenParentThemeMetadataIsUnreadable(): void
    {
        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('exists')->willReturn(true);

        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('getById')->willThrowException(new InvalidThemeMetaDataException());

        $checker = new ThemeParentCompatibilityChecker($themeConfigurationDao, $themeMetaDataByIdProvider);

        $this->expectException(ThemeInheritanceMetaDataException::class);

        $checker->validate(self::THEME_ID, self::PARENT_THEME_ID, self::SHOP_ID);
    }

    private function createChecker(
        bool $parentInstalled,
        string $parentVersion = '',
        array $declaredParentVersions = []
    ): ThemeParentCompatibilityChecker {
        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('exists')->willReturn($parentInstalled);

        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('getById')->willReturnCallback(
            fn(string $themeId): ThemeMetaData => $themeId === self::PARENT_THEME_ID
                ? (new ThemeMetaData())->setId(self::PARENT_THEME_ID)->setVersion($parentVersion)
                : (new ThemeMetaData())->setId(self::THEME_ID)->setParentVersions($declaredParentVersions)
        );

        return new ThemeParentCompatibilityChecker($themeConfigurationDao, $themeMetaDataByIdProvider);
    }
}
