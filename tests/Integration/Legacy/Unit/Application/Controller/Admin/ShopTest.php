<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop class
 */
class ShopTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Shop::Render() test case.
     */
    public function testRender()
    {
        $oView = oxNew('Shop');
        $this->assertSame('shop', $oView->render());
    }
}
