<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeViewService;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ThemeViewServiceTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testGetThemeMapsMetaDataFieldsAndActiveState(): void
    {
        $metaData = (new ThemeMetaData())
            ->setId('child')
            ->setTitle('Child')
            ->setDescription('A child theme')
            ->setThumbnail('thumb.png')
            ->setAuthor('OXID')
            ->setVersion('1.2.3');

        $theme = $this->createService(['child' => $metaData], active: true)->getTheme('child', self::SHOP_ID);

        $this->assertSame('child', $theme->getId());
        $this->assertSame('Child', $theme->getTitle());
        $this->assertSame('A child theme', $theme->getDescription());
        $this->assertSame('thumb.png', $theme->getThumbnail());
        $this->assertSame('OXID', $theme->getAuthor());
        $this->assertSame('1.2.3', $theme->getVersion());
        $this->assertTrue($theme->isActive());
        $this->assertSame('', $theme->getActivationError());
    }

    public function testGetThemeReportsInactiveTheme(): void
    {
        $theme = $this->createService(['child' => $this->metaData('child')], active: false)
            ->getTheme('child', self::SHOP_ID);

        $this->assertFalse($theme->isActive());
    }

    public function testHasParentThemeIsFalseWhenNoParentDeclared(): void
    {
        $service = $this->createService(['child' => $this->metaData('child')]);

        $this->assertFalse($service->hasParentTheme('child', self::SHOP_ID));
    }

    public function testHasParentThemeIsTrueForActiveThemeWithoutValidating(): void
    {
        $checker = $this->createMock(ThemeParentCompatibilityCheckerInterface::class);
        $checker->expects($this->never())->method('validate');

        $service = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            active: true,
            checker: $checker
        );

        $this->assertTrue($service->hasParentTheme('child', self::SHOP_ID));
    }

    public function testHasParentThemeIsFalseForCycle(): void
    {
        $service = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            checker: $this->throwingChecker(new ThemeParentCycleException())
        );

        $this->assertFalse($service->hasParentTheme('child', self::SHOP_ID));
    }

    public function testHasParentThemeIsTrueForIncompatibleVersion(): void
    {
        $service = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            checker: $this->throwingChecker(new ThemeParentCompatibilityException())
        );

        $this->assertTrue($service->hasParentTheme('child', self::SHOP_ID));
    }

    public function testHasParentThemeIsTrueForCompatibleParent(): void
    {
        $service = $this->createService(['child' => $this->metaData('child', 'parent')]);

        $this->assertTrue($service->hasParentTheme('child', self::SHOP_ID));
    }

    public function testGetParentThemeReturnsIdTitleAndVersions(): void
    {
        $service = $this->createService([
            'child' => $this->metaData('child', 'parent', ['1.0.0']),
            'parent' => $this->metaData('parent', title: 'Parent'),
        ]);

        $parent = $service->getParentTheme('child', self::SHOP_ID);

        $this->assertSame('parent', $parent->getId());
        $this->assertSame('Parent', $parent->getTitle());
        $this->assertSame(['1.0.0'], $parent->getVersions());
    }

    public function testGetParentThemeTitleIsEmptyWhenParentMetaDataIsUnreadable(): void
    {
        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->method('getById')->willReturnCallback(
            function (string $themeId): ThemeMetaData {
                if ($themeId === 'parent') {
                    throw new InvalidThemeMetaDataException();
                }

                return $this->metaData('child', 'parent', ['1.0.0']);
            }
        );

        $parent = (new ThemeViewService(
            $metaDataProvider,
            $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $this->createStub(ThemeStateServiceInterface::class),
            $this->createStub(LoggerInterface::class)
        ))->getParentTheme('child', self::SHOP_ID);

        $this->assertSame('parent', $parent->getId());
        $this->assertSame('', $parent->getTitle());
    }

    public function testGetThemeHasNoActivationErrorWhenNoParent(): void
    {
        $theme = $this->createService(['child' => $this->metaData('child')])->getTheme('child', self::SHOP_ID);

        $this->assertSame('', $theme->getActivationError());
    }

    public function testGetThemeHasNoActivationErrorForCompatibleParent(): void
    {
        $theme = $this->createService(['child' => $this->metaData('child', 'parent')])
            ->getTheme('child', self::SHOP_ID);

        $this->assertSame('', $theme->getActivationError());
    }

    public function testGetThemeReportsActivationErrorForCycle(): void
    {
        $theme = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            checker: $this->throwingChecker(new ThemeParentCycleException())
        )->getTheme('child', self::SHOP_ID);

        $this->assertSame('EXCEPTION_THEME_INHERITANCE_INVALID', $theme->getActivationError());
    }

    public function testGetThemeReportsActivationErrorForIncompatibleVersion(): void
    {
        $theme = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            checker: $this->throwingChecker(new ThemeParentCompatibilityException())
        )->getTheme('child', self::SHOP_ID);

        $this->assertSame('EXCEPTION_THEME_INHERITANCE_INVALID', $theme->getActivationError());
    }

    public function testGetThemeSkipsActivationCheckForActiveTheme(): void
    {
        $checker = $this->createMock(ThemeParentCompatibilityCheckerInterface::class);
        $checker->expects($this->never())->method('validate');

        $theme = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            active: true,
            checker: $checker
        )->getTheme('child', self::SHOP_ID);

        $this->assertSame('', $theme->getActivationError());
    }

    /** @param string[] $parentVersions */
    private function metaData(
        string $id,
        string $parentTheme = '',
        array $parentVersions = [],
        string $title = ''
    ): ThemeMetaData {
        return (new ThemeMetaData())
            ->setId($id)
            ->setParentTheme($parentTheme)
            ->setParentVersions($parentVersions)
            ->setTitle($title);
    }

    /** @param ThemeMetaData[] $metaDataById */
    private function createService(
        array $metaDataById,
        bool $active = false,
        ?ThemeParentCompatibilityCheckerInterface $checker = null
    ): ThemeViewService {
        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->method('getById')->willReturnCallback(
            fn (string $themeId): ThemeMetaData => $metaDataById[$themeId]
        );

        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        $themeStateService->method('isActive')->willReturn($active);

        return new ThemeViewService(
            $metaDataProvider,
            $checker ?? $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $themeStateService,
            $this->createStub(LoggerInterface::class)
        );
    }

    private function throwingChecker(\Throwable $exception): ThemeParentCompatibilityCheckerInterface
    {
        $checker = $this->createStub(ThemeParentCompatibilityCheckerInterface::class);
        $checker->method('validate')->willThrowException($exception);

        return $checker;
    }
}
