<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class SubShopSpecificFileCacheTest extends \OxidTestCase
{
    public function testSetGetCacheSubShopSpecific()
    {
        $sTest = "test val";

        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, array('getShopId'), array(), '', false);
        $shopIdCalculator->expects($this->any())->method('getShopId')->will($this->returnValue(1));

        $moduleCache = oxNew('oxSubShopSpecificFileCache', $shopIdCalculator);

        $moduleCache->setToCache("testKey", $sTest);
        $this->assertEquals($sTest, $moduleCache->getFromCache("testKey"));
    }

    public function testGetCacheFileName()
    {
        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, array('getShopId'), array(), '', false);
        $shopIdCalculator->expects($this->any())->method('getShopId')->will($this->returnValue(2));

        $moduleCache = $this->getProxyClass('oxSubShopSpecificFileCache', array($shopIdCalculator));

        $sExpt = "config.2.testval.txt";
        $this->assertEquals($sExpt, basename($moduleCache->getCacheFilePath("testVal")));
    }
}
