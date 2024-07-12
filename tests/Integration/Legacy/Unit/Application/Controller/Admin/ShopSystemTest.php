<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_System class
 */
class ShopSystemTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Shop_System::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Shop_System');
        $this->assertSame('shop_system', $oView->render());
    }
}
