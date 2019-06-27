<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleVariablesLocatorTest extends \OxidTestCase
{
    public function testGetModuleVarFromDB()
    {
        $cache = $this->getMock('oxFileCache');

        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, array('getShopId'), array(), '', false);
        $shopIdCalculator->expects($this->any())->method('getShopId')->will($this->returnValue($this->getShopId()));

        $moduleCache = oxNew('oxModuleVariablesLocator', $cache, $shopIdCalculator);
        $this->assertEquals(array("a7c40f631fc920687.20179984"), $moduleCache->getModuleVariable("aHomeCountry"));
    }
}
