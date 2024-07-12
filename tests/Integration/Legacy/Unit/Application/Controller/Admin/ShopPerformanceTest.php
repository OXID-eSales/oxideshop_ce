<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_Performance class
 */
class ShopPerformanceTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Shop_Performance::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_Performance');
        $this->assertSame('shop_performance', $oView->render());
    }
}
