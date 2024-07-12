<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Admin_Order class
 */
class AdminOrderTest extends \OxidTestCase
{

    /**
     * Admin_Order::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Admin_Order');
        $this->assertEquals('order', $oView->render());
    }
}
