<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentDepthExceededException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityChecker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class ThemeParentCompatibilityCheckerTest extends TestCase
{
    private const SHOP_ID = 1;

    #[DoesNotPerformAssertions]
    public function testValidatePassesForThemeWithoutParent(): void
    {
        $checker = $this->createChecker(
            childMetaData: $this->createMetaData('child'),
            parentInstalled: false
        );

        $checker->validate('child', self::SHOP_ID);
    }

    #[DoesNotPerformAssertions]
    public function testValidatePassesForCompatibleParentTheme(): void
    {
        $checker = $this->createChecker(
            childMetaData: $this->createMetaData('child', parentTheme: 'parent', parentVersions: ['1.0.0']),
            parentInstalled: true,
            parentMetaData: $this->createMetaData('parent', version: '1.0.0')
        );

        $checker->validate('child', self::SHOP_ID);
    }

    public function testValidateThrowsWhenThemeDeclaresItselfAsParent(): void
    {
        $checker = $this->createChecker(
            childMetaData: $this->createMetaData('child', parentTheme: 'child'),
            parentInstalled: true
        );

        $this->expectException(ThemeParentCycleException::class);

        $checker->validate('child', self::SHOP_ID);
    }

    public function testValidateThrowsWhenParentThemeIsNotInstalled(): void
    {
        $checker = $this->createChecker(
            childMetaData: $this->createMetaData('child', parentTheme: 'parent'),
            parentInstalled: false
        );

        $this->expectException(ThemeParentCompatibilityException::class);

        $checker->validate('child', self::SHOP_ID);
    }

    public function testValidateThrowsWhenParentThemeIsItselfAChildTheme(): void
    {
        $checker = $this->createChecker(
            childMetaData: $this->createMetaData('child', parentTheme: 'parent', parentVersions: ['1.0.0']),
            parentInstalled: true,
            parentMetaData: $this->createMetaData('parent', version: '1.0.0', parentTheme: 'grandparent')
        );

        $this->expectException(ThemeParentDepthExceededException::class);

        $checker->validate('child', self::SHOP_ID);
    }

    /** @param string[] $declaredParentVersions */
    #[DataProvider('incompatibleVersionDeclarationsProvider')]
    public function testValidateThrowsWhenVersionDeclarationsAreIncompatible(
        string $installedParentVersion,
        array $declaredParentVersions
    ): void {
        $checker = $this->createChecker(
            childMetaData: $this->createMetaData(
                'child',
                parentTheme: 'parent',
                parentVersions: $declaredParentVersions
            ),
            parentInstalled: true,
            parentMetaData: $this->createMetaData('parent', version: $installedParentVersion)
        );

        $this->expectException(ThemeParentCompatibilityException::class);

        $checker->validate('child', self::SHOP_ID);
    }

    public static function incompatibleVersionDeclarationsProvider(): array
    {
        return [
            'parent theme does not declare a version' => ['', ['1.0.0']],
            'child theme does not declare compatible parent versions' => ['1.0.0', []],
            'installed parent version does not match declared versions' => ['2.0.0', ['1.0.0', '1.1.0']],
        ];
    }

    /** @param string[] $parentVersions */
    private function createMetaData(
        string $id,
        string $version = '',
        string $parentTheme = '',
        array $parentVersions = []
    ): ThemeMetaData {
        return (new ThemeMetaData())
            ->setId($id)
            ->setVersion($version)
            ->setParentTheme($parentTheme)
            ->setParentVersions($parentVersions);
    }

    private function createChecker(
        ThemeMetaData $childMetaData,
        bool $parentInstalled,
        ?ThemeMetaData $parentMetaData = null
    ): ThemeParentCompatibilityChecker {
        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataMap = [$childMetaData->getId() => $childMetaData];
        if ($parentMetaData) {
            $metaDataMap[$parentMetaData->getId()] = $parentMetaData;
        }
        $metaDataProvider
            ->method('getById')
            ->willReturnCallback(fn (string $themeId) => $metaDataMap[$themeId]);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn($parentInstalled);

        return new ThemeParentCompatibilityChecker($dao, $metaDataProvider);
    }
}
