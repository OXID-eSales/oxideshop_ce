<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\View;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentDepthExceededException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\View\ThemeViewService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ThemeViewServiceTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testGetThemeExposesMetaDataFieldsAndActiveState(): void
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
    }

    public function testGetThemeReportsInactiveTheme(): void
    {
        $theme = $this->createService(['child' => $this->metaData('child')], active: false)
            ->getTheme('child', self::SHOP_ID);

        $this->assertFalse($theme->isActive());
    }

    public function testThemeWithoutDeclaredParentHasNoParentTheme(): void
    {
        $theme = $this->createService(['child' => $this->metaData('child')])->getTheme('child', self::SHOP_ID);

        $this->assertFalse($theme->hasParentTheme());
    }

    public function testThemeWithDeclaredParentHasParentTheme(): void
    {
        $theme = $this->createService(['child' => $this->metaData('child', 'parent')])
            ->getTheme('child', self::SHOP_ID);

        $this->assertTrue($theme->hasParentTheme());
    }

    public function testGetParentThemeCarriesIdTitleAndDeclaredVersions(): void
    {
        $service = $this->createService([
            'child' => $this->metaData('child', 'parent', ['1.0.0', '1.1.0']),
            'parent' => $this->metaData('parent', title: 'Parent Theme'),
        ]);

        $parentTheme = $service->getParentTheme('child', self::SHOP_ID);

        $this->assertSame('parent', $parentTheme->getId());
        $this->assertSame('Parent Theme', $parentTheme->getTitle());
        $this->assertSame(['1.0.0', '1.1.0'], $parentTheme->getVersions());
    }

    public function testGetParentThemeTitleIsEmptyWhenParentMetaDataIsUnreadable(): void
    {
        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->method('getById')->willReturnCallback(
            function (string $themeId): ThemeMetaData {
                if ($themeId === 'parent') {
                    throw new InvalidThemeMetaDataException('broken');
                }

                return $this->metaData('child', 'parent');
            }
        );
        $service = new ThemeViewService(
            $metaDataProvider,
            $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $this->createStub(ActiveThemeProviderInterface::class),
            $this->createStub(LoggerInterface::class)
        );

        $parentTheme = $service->getParentTheme('child', self::SHOP_ID);

        $this->assertSame('parent', $parentTheme->getId());
        $this->assertSame('', $parentTheme->getTitle());
    }

    public function testGetParentThemeIsCompatibleWhenValidationPasses(): void
    {
        $parentTheme = $this->createService(['child' => $this->metaData('child', 'parent')])
            ->getParentTheme('child', self::SHOP_ID);

        $this->assertTrue($parentTheme->isCompatible());
    }

    /** @param class-string<\Throwable> $validationFailureClass */
    #[DataProvider('compatibilityFailureProvider')]
    public function testGetParentThemeIsIncompatibleWhenValidationFails(string $validationFailureClass): void
    {
        $parentTheme = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            checker: $this->throwingChecker(new $validationFailureClass('validation failed'))
        )->getParentTheme('child', self::SHOP_ID);

        $this->assertFalse($parentTheme->isCompatible());
    }

    public static function compatibilityFailureProvider(): array
    {
        return [
            'parent cycle' => [ThemeParentCycleException::class],
            'inheritance depth exceeded' => [ThemeParentDepthExceededException::class],
            'incompatible version' => [ThemeParentCompatibilityException::class],
            'unreadable meta data' => [InvalidThemeMetaDataException::class],
        ];
    }

    public function testGetParentThemeOfActiveThemeIsCompatibleWithoutValidating(): void
    {
        $checker = $this->createMock(ThemeParentCompatibilityCheckerInterface::class);
        $checker->expects($this->never())->method('validate');

        $parentTheme = $this->createService(
            ['child' => $this->metaData('child', 'parent')],
            active: true,
            checker: $checker
        )->getParentTheme('child', self::SHOP_ID);

        $this->assertTrue($parentTheme->isCompatible());
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
            fn (string $themeId): ThemeMetaData => $metaDataById[$themeId] ?? $this->metaData($themeId)
        );

        $activeThemeProvider = $this->createStub(ActiveThemeProviderInterface::class);
        $activeThemeProvider->method('isActive')->willReturn($active);

        return new ThemeViewService(
            $metaDataProvider,
            $checker ?? $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $activeThemeProvider,
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
