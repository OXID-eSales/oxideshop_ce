<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class SubShopSpecificFileCacheTest extends \PHPUnit\Framework\TestCase
{
    public function testSetGetCacheSubShopSpecific()
    {
        $sTest = "test val";

        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, ['getShopId'], [], '', false);
        $shopIdCalculator->method('getShopId')->willReturn(1);

        $moduleCache = oxNew('oxSubShopSpecificFileCache', $shopIdCalculator);

        $moduleCache->setToCache("testKey", $sTest);
        $this->assertSame($sTest, $moduleCache->getFromCache("testKey"));
    }

    public function testGetCacheFileName()
    {
        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, ['getShopId'], [], '', false);
        $shopIdCalculator->method('getShopId')->willReturn(2);

        $moduleCache = $this->getProxyClass('oxSubShopSpecificFileCache', [$shopIdCalculator]);

        $sExpt = "config.2.testval.txt";
        $this->assertSame($sExpt, basename((string) $moduleCache->getCacheFilePath("testVal")));
    }
}
