<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Delivery class
 */
class DeliveryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Delivery::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Delivery');
        $this->assertSame('delivery', $oView->render());
    }
}
