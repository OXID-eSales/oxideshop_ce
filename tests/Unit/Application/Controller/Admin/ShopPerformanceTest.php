<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_Performance class
 */
class ShopPerformanceTest extends \OxidTestCase
{

    /**
     * Shop_Performance::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_Performance');
        $this->assertEquals('shop_performance.tpl', $oView->render());
    }
}
