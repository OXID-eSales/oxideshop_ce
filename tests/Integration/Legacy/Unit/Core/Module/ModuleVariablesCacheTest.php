<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * @package Unit\Core
 */
#[\PHPUnit\Framework\Attributes\Group('module')]
class ModuleVariablesCacheTest extends \OxidTestCase
{
    public function testSetGetCache()
    {
        $sTest = "test val";

        $moduleCache = oxNew('oxFileCache');

        $moduleCache->setToCache("testKey", $sTest);
        $this->assertEquals($sTest, $moduleCache->getFromCache("testKey"));
    }

    public function testSetGetCacheSubShopSpecific()
    {
        $sTest = "test val";

        $moduleCache = oxNew('oxFileCache');

        $moduleCache->setToCache("testKey", $sTest);
        $this->assertEquals($sTest, $moduleCache->getFromCache("testKey"));
    }

    public function testGetCacheFileName()
    {
        $moduleCache = $this->getProxyClass('oxFileCache');

        $sExpt = "config.all.testval.txt";
        $this->assertEquals($sExpt, basename((string) $moduleCache->getCacheFilePath("testVal")));
    }
}
