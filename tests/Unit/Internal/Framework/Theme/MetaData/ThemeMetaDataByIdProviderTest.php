<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemePathResolverInterface;
use PHPUnit\Framework\TestCase;

final class ThemeMetaDataByIdProviderTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'apex';

    public function testGetResolvesMetaDataUsingThemePathResolver(): void
    {
        $themePathResolver = $this->createStub(ThemePathResolverInterface::class);
        $themePathResolver->method('getFullThemePathFromConfiguration')->willReturn('/var/www/Application/views/apex');

        $expectedMetaData = (new ThemeMetaData())->setId(self::THEME_ID);

        $themeMetaDataProvider = $this->createMock(ThemeMetaDataProviderInterface::class);
        $themeMetaDataProvider->expects($this->once())
            ->method('get')
            ->with('/var/www/Application/views/apex')
            ->willReturn($expectedMetaData);

        $service = new ThemeMetaDataByIdProvider($themePathResolver, $themeMetaDataProvider);

        $this->assertSame($expectedMetaData, $service->getById(self::THEME_ID, self::SHOP_ID));
    }
}
