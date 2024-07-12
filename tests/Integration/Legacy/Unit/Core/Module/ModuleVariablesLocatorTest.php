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
class ModuleVariablesLocatorTest extends \PHPUnit\Framework\TestCase
{
    public function testGetModuleVarFromDB()
    {
        $cache = $this->getMock('oxFileCache');

        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, ['getShopId'], [], '', false);
        $shopIdCalculator->expects($this->any())->method('getShopId')->will($this->returnValue($this->getShopId()));

        $moduleCache = oxNew('oxModuleVariablesLocator', $cache, $shopIdCalculator);
        $this->assertEquals(["a7c40f631fc920687.20179984"], $moduleCache->getModuleVariable("aHomeCountry"));
    }
}
