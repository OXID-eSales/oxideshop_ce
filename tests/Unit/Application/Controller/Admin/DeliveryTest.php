<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Delivery class
 */
class DeliveryTest extends \OxidTestCase
{

    /**
     * Delivery::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Delivery');
        $this->assertEquals('delivery.tpl', $oView->render());
    }
}
