<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;

final class ThemeMetaDataByIdProviderTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'apex';

    public function testGetResolvesMetaDataByJoiningShopRootPathWithThemeSource(): void
    {
        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('get')->willReturn(
            (new ThemeConfiguration())->setId(self::THEME_ID)->setSource('Application/views/apex')
        );

        $expectedMetaData = (new ThemeMetaData())->setId(self::THEME_ID);

        $themeMetaDataProvider = $this->createMock(ThemeMetaDataProviderInterface::class);
        $themeMetaDataProvider->expects($this->once())
            ->method('get')
            ->with('/var/www/Application/views/apex')
            ->willReturn($expectedMetaData);

        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getShopRootPath')->willReturn('/var/www');

        $service = new ThemeMetaDataByIdProvider($themeConfigurationDao, $themeMetaDataProvider, $context);

        $this->assertSame($expectedMetaData, $service->get(self::THEME_ID, self::SHOP_ID));
    }
}
